import { usePage } from '@inertiajs/react';
import StickerPortrait from '@/Components/StickerPortrait';
import TornEdge from '@/Components/TornEdge';
import useTrans from '@/lib/useTrans';

export default function Band() {
    const { profile } = usePage().props;
    const { t } = useTrans();

    return (
        <section className="relative bg-band pb-16 text-on-band md:pb-20" aria-label={t('Portrait')}>
            <TornEdge position="top" color="fill-band" />

            {/* Nom géant en fond, décoratif */}
            <div aria-hidden="true" className="absolute inset-0 flex items-center overflow-hidden">
                <p className="whitespace-nowrap font-display text-[18vw] uppercase leading-none opacity-90">
                    {profile.name} {profile.name} {profile.name}
                </p>
            </div>

            <div className="relative flex justify-center">
                <StickerPortrait
                    src={profile.images.portrait}
                    alt={profile.images.portrait_alt}
                    className="-mt-24 md:-mt-36"
                />
            </div>

            <TornEdge position="bottom" color="fill-band" />
        </section>
    );
}
