import { Link, useForm } from '@inertiajs/react';
import { IconArrowLeft } from '@tabler/icons-react';
import AdminLayout from '@/Layouts/AdminLayout';
import cn from '@/lib/cn';

const input =
    'w-full min-h-11 border-2 border-ink bg-surface px-3 py-2 text-base text-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent sm:text-sm';

function Error({ id, message }) {
    return message ? <p id={id} className="mt-1 text-sm text-danger">{message}</p> : null;
}

function Field({ field, form }) {
    const { data, setData, errors } = form;
    const { name, type, label, required, help, options, accept } = field;
    const id = `f-${name}`;
    const err = (key) => errors[key];
    const describe = (key) => ({ 'aria-invalid': err(key) ? true : undefined, 'aria-describedby': err(key) ? `${id}-${key}-error` : undefined });
    const title = (
        <span className="mb-2 block text-sm font-bold uppercase">
            {label} {required && <span className="text-accent-ink" aria-hidden="true">*</span>}
        </span>
    );

    if (type === 'translatable' || type === 'translatable_textarea') {
        const Tag = type === 'translatable' ? 'input' : 'textarea';
        return (
            <fieldset>
                <legend className="mb-2 text-sm font-bold uppercase">
                    {label} {required && <span className="text-accent-ink" aria-hidden="true">*</span>}
                </legend>
                <div className="grid gap-3 md:grid-cols-2">
                    {['fr', 'en'].map((lang) => (
                        <div key={lang}>
                            <label htmlFor={`${id}-${lang}`} className="mb-1 block font-mono text-xs uppercase text-muted">
                                {lang === 'fr' ? 'Français' : 'Anglais (facultatif)'}
                            </label>
                            <Tag
                                id={`${id}-${lang}`}
                                rows={type === 'translatable_textarea' ? 5 : undefined}
                                value={data[name]?.[lang] ?? ''}
                                onChange={(e) => setData(name, { ...data[name], [lang]: e.target.value })}
                                className={cn(input, type === 'translatable_textarea' && 'resize-y')}
                                {...describe(`${name}.${lang}`)}
                            />
                            <Error id={`${id}-${name}.${lang}-error`} message={err(`${name}.${lang}`)} />
                        </div>
                    ))}
                </div>
                {help && <p className="mt-1 text-sm text-muted">{help}</p>}
            </fieldset>
        );
    }

    if (type === 'checkbox') {
        return (
            <label className="flex min-h-11 items-center gap-3 font-bold">
                <input type="checkbox" checked={!!data[name]} onChange={(e) => setData(name, e.target.checked)} className="size-5 accent-accent" />
                {label}
            </label>
        );
    }

    if (type === 'image' || type === 'file') {
        const current = form.current?.[name];
        return (
            <div>
                <label htmlFor={id}>{title}</label>
                {current && type === 'image' && <img src={current} alt="" className="mb-3 h-32 w-auto border-2 border-ink object-cover" />}
                {current && type === 'file' && (
                    <a href={current} target="_blank" rel="noopener noreferrer" className="mb-3 inline-block font-bold text-accent-ink underline">
                        Voir le fichier actuel
                    </a>
                )}
                <input id={id} type="file" accept={accept ?? 'image/*'} onChange={(e) => setData(name, e.target.files[0] ?? null)} className="block w-full text-sm file:mr-3 file:min-h-11 file:rounded-full file:border-2 file:border-ink file:bg-surface file:px-4 file:font-bold" {...describe(name)} />
                {help && <p className="mt-1 text-sm text-muted">{help}{current ? ' Laisser vide pour garder le fichier actuel.' : ''}</p>}
                <Error id={`${id}-${name}-error`} message={err(name)} />
            </div>
        );
    }

    const common = { id, value: data[name] ?? '', onChange: (e) => setData(name, e.target.value), className: input, ...describe(name) };
    let control;
    if (type === 'textarea') control = <textarea rows={5} {...common} />;
    else if (type === 'select')
        control = (
            <select {...common}>
                {options.map((o) => (
                    <option key={o.value} value={o.value}>{o.label}</option>
                ))}
            </select>
        );
    else {
        const htmlType = { url: 'url', email: 'email', number: 'number', year: 'number', date: 'date' }[type] ?? 'text';
        control = <input type={htmlType} {...common} />;
    }

    return (
        <div>
            <label htmlFor={id}>{title}</label>
            {control}
            {help && <p className="mt-1 text-sm text-muted">{help}</p>}
            <Error id={`${id}-${name}-error`} message={err(name)} />
        </div>
    );
}

export default function Form({ title, back, action, method, fields, values }) {
    // Les champs fichier partent vides : on n'envoie un fichier que s'il a été choisi
    const fileFields = fields.filter((f) => f.type === 'image' || f.type === 'file').map((f) => f.name);
    const initial = Object.fromEntries(Object.entries(values).map(([k, v]) => [k, fileFields.includes(k) ? null : v]));
    const form = useForm({ ...initial, _method: method });
    form.current = values;

    const submit = (e) => {
        e.preventDefault();
        form.post(action, { forceFormData: fileFields.length > 0, preserveScroll: true });
    };

    return (
        <AdminLayout title={title}>
            <Link href={back} className="inline-flex min-h-11 items-center gap-2 font-bold">
                <IconArrowLeft size={18} aria-hidden="true" /> Retour
            </Link>
            <form onSubmit={submit} className="mt-4 max-w-4xl space-y-6 border-2 border-ink bg-surface p-5 sm:p-8" noValidate>
                {fields.map((f) => (
                    <Field key={f.name} field={f} form={form} />
                ))}
                {Object.keys(form.errors).length > 0 && (
                    <p role="alert" className="text-sm font-bold text-danger">Certains champs sont à corriger.</p>
                )}
                <button type="submit" disabled={form.processing} className="min-h-11 rounded-full bg-accent px-6 font-bold text-on-accent disabled:opacity-60 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">
                    {form.processing ? 'Enregistrement…' : 'Enregistrer'}
                </button>
            </form>
        </AdminLayout>
    );
}
