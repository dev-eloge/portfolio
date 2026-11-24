import { Head, useForm, usePage } from '@inertiajs/react';
import { IconBrandGithub, IconBrandLinkedin, IconCircleCheck, IconMail, IconMapPin } from '@tabler/icons-react';
import Button from '@/Components/Button';
import SectionTitle from '@/Components/SectionTitle';
import AppLayout from '@/Layouts/AppLayout';
import cn from '@/lib/cn';
import useTrans from '@/lib/useTrans';

const input =
    'w-full min-h-11 border-2 border-ink bg-surface px-4 py-2.5 text-ink placeholder:text-muted ' +
    'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent';

function Field({ id, label, optional, error, children }) {
    const { t } = useTrans();

    return (
        <div>
            <label htmlFor={id} className="mb-2 block text-sm font-bold uppercase">
                {label} {optional && <span className="font-normal normal-case text-muted">{t('(facultatif)')}</span>}
            </label>
            {children}
            {error && (
                <p id={`${id}-error`} className="mt-1 text-sm text-danger">
                    {error}
                </p>
            )}
        </div>
    );
}

export default function Contact() {
    const { profile, flash } = usePage().props;
    const { t } = useTrans();
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        email: '',
        subject: '',
        message: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post('/contact', {
            preserveScroll: true,
            onSuccess: () => reset(),
        });
    };

    const aria = (field) => ({
        'aria-invalid': errors[field] ? true : undefined,
        'aria-describedby': errors[field] ? `${field}-error` : undefined,
    });

    const links = [
        profile.email && { href: `mailto:${profile.email}`, label: profile.email, Icon: IconMail, external: false },
        profile.github && { href: profile.github, label: 'GitHub', Icon: IconBrandGithub, external: true },
        profile.linkedin && { href: profile.linkedin, label: 'LinkedIn', Icon: IconBrandLinkedin, external: true },
    ].filter(Boolean);

    return (
        <AppLayout>
            <Head title={t('Contact')} />
            <section className="mx-auto max-w-6xl px-4 py-16 sm:px-6 md:py-24 lg:px-8">
                <SectionTitle as="h1" title={t('Contact')} separator subtitle={t('Décrivez votre projet ou votre besoin, je vous réponds par e-mail.')} />

                <div className="mt-12 grid gap-12 lg:grid-cols-[1fr_20rem]">
                    <form onSubmit={submit} noValidate className="space-y-6">
                        {flash.success && (
                            <p role="status" className="flex items-center gap-2 border-2 border-success bg-surface p-4 font-bold text-success">
                                <IconCircleCheck size={22} aria-hidden="true" />
                                {flash.success}
                            </p>
                        )}

                        <div className="grid gap-6 sm:grid-cols-2">
                            <Field id="name" label={t('Nom')} error={errors.name}>
                                <input id="name" type="text" autoComplete="name" value={data.name} onChange={(e) => setData('name', e.target.value)} className={input} {...aria('name')} />
                            </Field>
                            <Field id="email" label={t('E-mail')} error={errors.email}>
                                <input id="email" type="email" autoComplete="email" value={data.email} onChange={(e) => setData('email', e.target.value)} className={input} {...aria('email')} />
                            </Field>
                        </div>

                        <Field id="subject" label={t('Sujet')} optional error={errors.subject}>
                            <input id="subject" type="text" value={data.subject} onChange={(e) => setData('subject', e.target.value)} className={input} {...aria('subject')} />
                        </Field>

                        <Field id="message" label={t('Message')} error={errors.message}>
                            <textarea id="message" rows={7} value={data.message} onChange={(e) => setData('message', e.target.value)} className={cn(input, 'resize-y')} {...aria('message')} />
                        </Field>

                        <Button type="submit" disabled={processing} className="disabled:opacity-60">
                            {processing ? t('Envoi…') : t('Envoyer')}
                        </Button>
                    </form>

                    <aside className="h-fit rotate-1 border-2 border-ink bg-surface p-6 motion-reduce:rotate-0 md:p-8">
                        <h2 className="font-display text-2xl uppercase">{t('Me joindre')}</h2>
                        {links.length > 0 && (
                            <ul className="mt-6 space-y-3">
                                {links.map(({ href, label, Icon, external }) => (
                                    <li key={label}>
                                        <a
                                            href={href}
                                            {...(external ? { target: '_blank', rel: 'noopener noreferrer' } : {})}
                                            className="inline-flex min-h-11 items-center gap-3 font-bold underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent"
                                        >
                                            <Icon size={22} aria-hidden="true" />
                                            {label}
                                        </a>
                                    </li>
                                ))}
                            </ul>
                        )}
                        {profile.location && (
                            <p className="mt-6 flex items-center gap-3 border-t-2 border-ink pt-6">
                                <IconMapPin size={22} aria-hidden="true" />
                                <span>
                                    {profile.location}
                                    {profile.work_mode && <span className="block text-sm text-muted">{profile.work_mode}</span>}
                                </span>
                            </p>
                        )}
                    </aside>
                </div>
            </section>
        </AppLayout>
    );
}
