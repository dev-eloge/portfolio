import { usePage } from '@inertiajs/react';
import { IconAlertTriangle, IconSend, IconSparkles, IconX } from '@tabler/icons-react';
import axios from 'axios';
import { useEffect, useRef, useState } from 'react';
import { useAssistant } from '@/Components/Assistant/AssistantContext';
import cn from '@/lib/cn';
import useTrans from '@/lib/useTrans';

const SUGGESTIONS = ['Qui est :name, en quelques lignes ?', 'Quels sont ses projets les plus représentatifs ?', 'Quels services propose-t-il ?'];
const PROJECT_SUGGESTIONS = ['Explique ce projet.', 'Quelle stack a été utilisée, et pourquoi ?', 'Quel problème ce projet résout-il ?'];

const focusRing = 'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent';

export default function AiAssistant() {
    const { profile, assistant: config } = usePage().props;
    const { t } = useTrans();
    const assistant = useAssistant();
    const [conversation, setConversation] = useState([]);
    const [input, setInput] = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);
    const panelRef = useRef(null);
    const inputRef = useRef(null);
    const launcherRef = useRef(null);
    const listRef = useRef(null);

    const project = config?.projects.find((p) => p.slug === assistant.project) ?? null;
    const suggestions = project ? PROJECT_SUGGESTIONS : SUGGESTIONS;

    // Nouveau sujet (projet choisi ou retiré) : nouvelle conversation
    useEffect(() => {
        setConversation([]);
        setError(null);
    }, [assistant.project]);

    // Focus : sur le champ à l'ouverture, puis retour sur le bouton à la fermeture
    const wasOpen = useRef(false);
    useEffect(() => {
        if (assistant.isOpen) inputRef.current?.focus();
        else if (wasOpen.current) launcherRef.current?.focus();
        wasOpen.current = assistant.isOpen;
    }, [assistant.isOpen]);

    useEffect(() => {
        listRef.current?.scrollTo({ top: listRef.current.scrollHeight });
    }, [conversation, loading]);

    if (!config) return null;

    const close = () => assistant.close();

    const send = async (text) => {
        const question = text.trim();
        if (!question || loading) return;

        const next = [...conversation, { role: 'user', content: question }];
        setConversation(next);
        setInput('');
        setError(null);
        setLoading(true);

        try {
            const { data } = await axios.post('/assistant', {
                project: assistant.project,
                messages: next.slice(-12),
            });
            setConversation([...next, { role: 'assistant', content: data.answer }]);
        } catch (e) {
            // La question non répondue est retirée pour pouvoir la renvoyer
            setConversation(conversation);
            setInput(question);
            setError(e.response?.data?.message ?? t("La réponse n'a pas pu être générée. Réessayez."));
        } finally {
            setLoading(false);
        }
    };

    const onKeyDown = (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            send(input);
        }
    };

    return (
        <>
            {!assistant.isOpen && (
                <button
                    ref={launcherRef}
                    type="button"
                    onClick={() => assistant.open({ project: null })}
                    className={cn(
                        'fixed bottom-4 right-4 z-40 inline-flex min-h-11 items-center gap-2 rounded-full bg-accent px-5 py-2.5 font-bold text-on-accent',
                        'shadow-[0_1px_0_rgba(0,0,0,.08),0_10px_20px_-10px_rgba(0,0,0,.35)] md:bottom-6 md:right-6',
                        focusRing,
                    )}
                >
                    <IconSparkles size={20} aria-hidden="true" />
                    {t("Eloge IA")}
                </button>
            )}

            {assistant.isOpen && (
                <section
                    ref={panelRef}
                    role="dialog"
                    aria-modal="false"
                    aria-labelledby="assistant-title"
                    onKeyDown={(e) => e.key === 'Escape' && close()}
                    className="fixed inset-x-2 bottom-2 z-50 flex max-h-[calc(100dvh-1rem)] flex-col border-2 border-ink bg-surface sm:inset-x-auto sm:bottom-6 sm:right-6 sm:h-[min(40rem,calc(100dvh-3rem))] sm:w-[26rem]"
                >
                    <header className="flex items-center justify-between gap-3 border-b-2 border-ink px-4 py-3">
                        <h2 id="assistant-title" className="flex items-center gap-2 font-display text-lg uppercase">
                            <IconSparkles size={20} aria-hidden="true" />
                            {t('Assistant IA')}
                        </h2>
                        <button type="button" onClick={close} aria-label={t("Fermer l'assistant")} className={cn('grid size-11 place-items-center', focusRing)}>
                            <IconX size={22} aria-hidden="true" />
                        </button>
                    </header>

                    {project && (
                        <div className="flex items-center justify-between gap-3 border-b-2 border-ink px-4 py-2 text-sm">
                            <p>
                                <span className="font-bold">{t('Projet :')}</span> {project.title}
                            </p>
                            <button
                                type="button"
                                onClick={() => assistant.setState((s) => ({ ...s, project: null }))}
                                aria-label={t('Retirer le projet')}
                                className={cn('grid size-11 shrink-0 place-items-center', focusRing)}
                            >
                                <IconX size={18} aria-hidden="true" />
                            </button>
                        </div>
                    )}

                    <div ref={listRef} className="flex-1 space-y-4 overflow-y-auto px-4 py-4" aria-live="polite">
                        {conversation.length === 0 && (
                            <div>
                                <p className="text-sm text-muted">
                                    {project
                                        ? t('Questions sur le projet :title :', { title: project.title })
                                        : t('Questions suggérées :')}
                                </p>
                                <ul className="mt-3 space-y-2">
                                    {suggestions.map((s) => {
                                        const text = t(s, { name: profile.name });
                                        return (
                                            <li key={s}>
                                                <button
                                                    type="button"
                                                    onClick={() => send(text)}
                                                    className={cn('min-h-11 w-full border-2 border-ink bg-paper px-3 py-2 text-left text-sm hover:bg-surface', focusRing)}
                                                >
                                                    {text}
                                                </button>
                                            </li>
                                        );
                                    })}
                                </ul>
                            </div>
                        )}

                        {conversation.map((msg, i) => (
                            <div key={i} className={msg.role === 'user' ? 'ml-8' : 'mr-4'}>
                                <p className="mb-1 text-xs font-bold uppercase text-muted">{msg.role === 'user' ? t('Vous') : t('Assistant')}</p>
                                <p className={cn('whitespace-pre-line text-sm leading-relaxed', msg.role === 'user' ? 'bg-ink p-3 text-paper' : 'border-2 border-ink bg-paper p-3')}>
                                    {msg.content}
                                </p>
                            </div>
                        ))}

                        {loading && (
                            <p className="flex items-center gap-2 text-sm text-muted">
                                <span className="size-2 animate-pulse rounded-full bg-accent motion-reduce:animate-none" aria-hidden="true" />
                                {t('Réflexion…')}
                            </p>
                        )}

                        {error && (
                            <p role="alert" className="flex items-start gap-2 border-2 border-danger p-3 text-sm text-danger">
                                <IconAlertTriangle size={18} aria-hidden="true" className="mt-0.5 shrink-0" />
                                {error}
                            </p>
                        )}
                    </div>

                    <form
                        onSubmit={(e) => {
                            e.preventDefault();
                            send(input);
                        }}
                        className="flex items-end gap-2 border-t-2 border-ink p-3"
                    >
                        <label htmlFor="assistant-input" className="sr-only">
                            {t('Votre question')}
                        </label>
                        <textarea
                            id="assistant-input"
                            ref={inputRef}
                            rows={2}
                            maxLength={8000}
                            value={input}
                            onChange={(e) => setInput(e.target.value)}
                            onKeyDown={onKeyDown}
                            placeholder={t('Votre question')}
                            className={cn('min-h-11 flex-1 resize-none border-2 border-ink bg-paper px-3 py-2 text-base text-ink placeholder:text-muted sm:text-sm', focusRing)}
                        />
                        <button
                            type="submit"
                            disabled={loading || !input.trim()}
                            aria-label={t('Envoyer')}
                            className={cn('grid size-11 shrink-0 place-items-center rounded-full bg-accent text-on-accent disabled:opacity-50', focusRing)}
                        >
                            <IconSend size={20} aria-hidden="true" />
                        </button>
                    </form>
                    <p className="px-3 pb-2 text-xs text-muted">{t("Réponses générées par IA à partir du contenu du site. Elles peuvent contenir des erreurs.")}</p>
                </section>
            )}
        </>
    );
}
