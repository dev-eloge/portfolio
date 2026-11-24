import { createContext, useCallback, useContext, useMemo, useState } from 'react';

const AssistantContext = createContext(null);

export function AssistantProvider({ children }) {
    // `isOpen` (état) ne doit pas porter le même nom que la fonction `open()`
    const [state, setState] = useState({ isOpen: false, project: null });

    const open = useCallback((options = {}) => {
        setState((s) => ({ ...s, ...options, isOpen: true }));
    }, []);

    const close = useCallback(() => setState((s) => ({ ...s, isOpen: false })), []);

    const value = useMemo(() => ({ ...state, open, close, setState }), [state, open, close]);

    return <AssistantContext.Provider value={value}>{children}</AssistantContext.Provider>;
}

/**
 * Ouvre / ferme l'assistant depuis n'importe quel composant (ex. carte projet).
 * { isOpen, project, open({ project }), close(), setState }
 */
export function useAssistant() {
    return useContext(AssistantContext);
}
