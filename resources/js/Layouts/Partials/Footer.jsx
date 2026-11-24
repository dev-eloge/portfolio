import { usePage } from '@inertiajs/react';
import { IconBrandGithub, IconBrandLinkedin, IconMail, IconMapPin } from '@tabler/icons-react';
import useTrans from '@/lib/useTrans';

export default function Footer() {
    const { profile } = usePage().props;
    const { t } = useTrans();

    const socials = [
        profile.github && { href: profile.github, label: 'GitHub', Icon: IconBrandGithub, external: true },
        profile.linkedin && { href: profile.linkedin, label: 'LinkedIn', Icon: IconBrandLinkedin, external: true },
        profile.email && { href: `mailto:${profile.email}`, label: t('E-mail'), Icon: IconMail, external: false },
    ].filter(Boolean);

    return (
        <footer className="relative z-10 border-t-2 border-ink">
            <div className="mx-auto flex max-w-6xl flex-col gap-6 px-4 py-10 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">
                <div className="space-y-1 text-sm">
                    <p className="font-bold">
                        © {new Date().getFullYear()} {profile.name}
                    </p>
                    {profile.location && (
                        <p className="flex items-center gap-1.5 text-muted">
                            <IconMapPin size={16} aria-hidden="true" />
                            {profile.location}
                            {profile.work_mode && ` · ${profile.work_mode}`}
                        </p>
                    )}
                </div>

                {socials.length > 0 && (
                    <ul className="flex gap-3">
                        {socials.map(({ href, label, Icon, external }) => (
                            <li key={label}>
                                <a
                                    href={href}
                                    aria-label={label}
                                    {...(external ? { target: '_blank', rel: 'noopener noreferrer' } : {})}
                                    className="grid size-11 place-items-center rounded-full border-2 border-ink transition-colors hover:bg-surface focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent"
                                >
                                    <Icon size={20} aria-hidden="true" />
                                </a>
                            </li>
                        ))}
                    </ul>
                )}
            </div>
        </footer>
    );
}
