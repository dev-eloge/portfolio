<?php

namespace App\Http\Controllers;

use Anthropic\Core\Exceptions\APIConnectionException;
use Anthropic\Core\Exceptions\APIException;
use Anthropic\Core\Exceptions\AuthenticationException;
use Anthropic\Core\Exceptions\PermissionDeniedException;
use Anthropic\Core\Exceptions\RateLimitException;
use App\Models\Project;
use App\Services\PortfolioAssistant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AssistantController extends Controller
{
    public function __invoke(Request $request, PortfolioAssistant $assistant): JsonResponse
    {
        if (! PortfolioAssistant::enabled()) {
            return $this->error(__("L'assistant n'est pas disponible pour le moment."), 503);
        }

        $data = $request->validate([
            'project' => ['nullable', 'string', 'exists:projects,slug'],
            'messages' => ['required', 'array', 'min:1', 'max:12'],
            'messages.*.role' => ['required', Rule::in(['user', 'assistant'])],
            'messages.*.content' => ['required', 'string', 'max:8000'],
        ]);

        // L'API attend une conversation qui commence et se termine par l'utilisateur
        $messages = array_values($data['messages']);
        if ($messages[0]['role'] !== 'user' || end($messages)['role'] !== 'user') {
            return $this->error(__('La réponse n\'a pas pu être générée. Réessayez.'), 422);
        }

        $project = isset($data['project']) ? Project::where('slug', $data['project'])->first() : null;

        try {
            $result = $assistant->ask($messages, $project);
        } catch (RateLimitException $e) {
            report($e);

            return $this->error(__("L'assistant est très sollicité. Réessayez dans une minute."), 429);
        } catch (AuthenticationException|PermissionDeniedException $e) {
            // Clé invalide ou sans droits : problème de configuration côté serveur
            report($e);

            return $this->error(__("L'assistant n'est pas disponible pour le moment."), 503);
        } catch (APIConnectionException|APIException $e) {
            report($e);

            return $this->error(__('La réponse n\'a pas pu être générée. Réessayez.'), 502);
        }

        if ($result['refused'] || $result['answer'] === '') {
            return response()->json([
                'answer' => __("Je ne peux pas répondre à cette question. Posez-moi une question sur les projets, les compétences ou le parcours."),
            ]);
        }

        return response()->json(['answer' => $result['answer']]);
    }

    private function error(string $message, int $status): JsonResponse
    {
        return response()->json(['message' => $message], $status);
    }
}
