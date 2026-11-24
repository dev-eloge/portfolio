import { IconClover, IconSparkles, IconStar } from '@tabler/icons-react';
import cn from '@/lib/cn';

export default function Sparkles({ className = '' }) {
    return (
        <div className={cn('flex items-center gap-3 text-ink', className)} aria-hidden="true">
            <IconSparkles size={28} stroke={2} />
            <IconStar size={28} stroke={2} />
            <IconClover size={28} stroke={2} />
        </div>
    );
}
