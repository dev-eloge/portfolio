import { Head } from '@inertiajs/react';
import SectionTitle from '@/Components/SectionTitle';
import Timeline from '@/Components/Timeline';
import AppLayout from '@/Layouts/AppLayout';
import useTrans from '@/lib/useTrans';

export default function Experience({ experiences }) {
    const { t } = useTrans();

    return (
        <AppLayout>
            <Head title={t('Expérience')} />
            <section className="mx-auto max-w-6xl px-4 py-16 sm:px-6 md:py-24 lg:px-8">
                <SectionTitle as="h1" title={t('Expérience')} separator />
                {experiences.length > 0 ? (
                    <div className="mt-12">
                        <Timeline experiences={experiences} />
                    </div>
                ) : (
                    <p className="mt-12 border-2 border-ink bg-surface p-8 text-lg">{t('Aucune expérience renseignée pour le moment.')}</p>
                )}
            </section>
        </AppLayout>
    );
}
