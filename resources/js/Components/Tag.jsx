export default function Tag({ children }) {
    return (
        <span className="inline-flex items-center rounded-full border border-ink px-3 py-1 font-mono text-xs text-ink">
            {children}
        </span>
    );
}
