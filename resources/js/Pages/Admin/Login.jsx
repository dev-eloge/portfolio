import { Head, useForm, usePage } from '@inertiajs/react';
import { useAdminDarkMode } from '@/Layouts/AdminLayout';

const input = 'w-full min-h-11 border-2 border-ink bg-surface px-4 py-2.5 text-base text-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent';

export default function Login() {
    useAdminDarkMode();
    const { adminPath } = usePage().props;
    const { data, setData, post, processing, errors } = useForm({ email: '', password: '', remember: false });

    return (
        <main className="flex min-h-screen items-center justify-center bg-paper px-4">
            <Head title="Connexion — Admin">
                <meta name="robots" content="noindex, nofollow" />
            </Head>
            <form
                onSubmit={(e) => {
                    e.preventDefault();
                    post(`${adminPath}/connexion`);
                }}
                className="w-full max-w-sm space-y-5 border-2 border-ink bg-surface p-6"
            >
                <h1 className="font-display text-2xl uppercase">Connexion</h1>
                <div>
                    <label htmlFor="email" className="mb-2 block text-sm font-bold uppercase">E-mail</label>
                    <input id="email" type="email" autoComplete="username" value={data.email} onChange={(e) => setData('email', e.target.value)} className={input} aria-invalid={errors.email ? true : undefined} aria-describedby={errors.email ? 'email-error' : undefined} />
                    {errors.email && <p id="email-error" className="mt-1 text-sm text-danger">{errors.email}</p>}
                </div>
                <div>
                    <label htmlFor="password" className="mb-2 block text-sm font-bold uppercase">Mot de passe</label>
                    <input id="password" type="password" autoComplete="current-password" value={data.password} onChange={(e) => setData('password', e.target.value)} className={input} />
                </div>
                <label className="flex min-h-11 items-center gap-2 text-sm">
                    <input type="checkbox" checked={data.remember} onChange={(e) => setData('remember', e.target.checked)} className="size-5 accent-accent" />
                    Rester connecté
                </label>
                <button type="submit" disabled={processing} className="min-h-11 w-full rounded-full bg-accent font-bold text-on-accent disabled:opacity-60 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">
                    {processing ? 'Connexion…' : 'Se connecter'}
                </button>
            </form>
        </main>
    );
}
