export default function PaperGrain() {
    return (
        <svg aria-hidden="true" className="pointer-events-none fixed inset-0 z-0 size-full opacity-[0.05]">
            <filter id="paper-grain">
                <feTurbulence type="fractalNoise" baseFrequency="0.8" numOctaves="3" stitchTiles="stitch" />
            </filter>
            <rect width="100%" height="100%" filter="url(#paper-grain)" />
        </svg>
    );
}
