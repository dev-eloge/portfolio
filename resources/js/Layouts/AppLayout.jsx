import AiAssistant from '@/Components/Assistant/AiAssistant';
import { AssistantProvider } from '@/Components/Assistant/AssistantContext';
import PaperGrain from '@/Components/PaperGrain';
import Footer from '@/Layouts/Partials/Footer';
import Navbar from '@/Layouts/Partials/Navbar';
import useTrans from '@/lib/useTrans';

export default function AppLayout({ children }) {
    const { t } = useTrans();

    return (
        <AssistantProvider>
            <div className="flex min-h-screen flex-col">
                <PaperGrain />
                <a
                    href="#contenu"
                    className="sr-only z-[60] bg-accent px-4 py-2 font-bold text-on-accent focus:not-sr-only focus:fixed focus:left-4 focus:top-4"
                >
                    {t('Aller au contenu')}
                </a>
                <Navbar />
                <main id="contenu" className="relative z-10 flex-1 overflow-x-clip">
                    {children}
                </main>
                <Footer />
                <AiAssistant />
            </div>
        </AssistantProvider>
    );
}
