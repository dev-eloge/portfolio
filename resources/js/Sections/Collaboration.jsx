import { usePage } from '@inertiajs/react';
import Button from '@/Components/Button';
import Sparkles from '@/Components/Sparkles';
import useTrans from '@/lib/useTrans';

export default function Collaboration() {
    const { profile } = usePage().props;
    const { t } = useTrans();

    return (
        <section className="mx-auto max-w-6xl px-4 py-20 sm:px-6 md:py-28 lg:px-8">
            <div className="border-2 border-ink bg-surface p-8 md:p-14">
                <Sparkles />
                <h2 className="mt-6 font-display text-4xl uppercase leading-[0.9] md:text-6xl">{t('Un projet en tête ?')}</h2>
                <p className="mt-4 max-w-xl text-lg leading-relaxed">{t('Parlons de ce que vous voulez construire.')}</p>
                <div className="mt-8 flex flex-wrap items-center gap-4">
                    <Button href="/contact">{t('Me contacter')}</Button>
                    <Button variant="secondary" href="/a-propos">
                        {t('Lire mon parcours')}
                    </Button>
                    {profile.cv && (
                        <Button variant="link" href={profile.cv} download>
                            {t('Télécharger le CV')}
                        </Button>
                    )}
                </div>
            </div>
        </section>
    );
}
