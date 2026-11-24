import { Head } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import useTrans from '@/lib/useTrans';
import Collaboration from '@/Sections/Collaboration';
import FeaturedProjects from '@/Sections/FeaturedProjects';
import Hero from '@/Sections/Hero';
import Services from '@/Sections/Services';

export default function Home({ highlights, services, featuredProjects }) {
    const { t } = useTrans();

    return (
        <AppLayout>
            <Head title={t('Accueil')} />
            <Hero highlights={highlights} />
            <Services services={services} />
            <FeaturedProjects projects={featuredProjects} />
            <Collaboration />
        </AppLayout>
    );
}
