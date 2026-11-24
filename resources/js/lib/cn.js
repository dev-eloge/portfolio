/** Combine des classes conditionnelles : cn('a', cond && 'b') */
export default function cn(...classes) {
    return classes.filter(Boolean).join(' ');
}
