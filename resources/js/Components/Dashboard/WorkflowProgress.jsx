import {
    Check,
    Circle,
    Clock3,
    X,
} from 'lucide-react';

const stateClasses = {
    completed: {
        circle:
            'border-emerald-600 bg-emerald-600 text-white',
        line:
            'bg-emerald-500',
        label:
            'text-emerald-700',
        icon:
            Check,
    },

    current: {
        circle:
            'border-blue-600 bg-blue-50 text-blue-700',
        line:
            'bg-slate-200',
        label:
            'text-blue-700',
        icon:
            Clock3,
    },

    rejected: {
        circle:
            'border-red-600 bg-red-600 text-white',
        line:
            'bg-red-300',
        label:
            'text-red-700',
        icon:
            X,
    },

    pending: {
        circle:
            'border-slate-300 bg-white text-slate-400',
        line:
            'bg-slate-200',
        label:
            'text-slate-500',
        icon:
            Circle,
    },
};

export default function WorkflowProgress({
    steps = [],
}) {
    if (!Array.isArray(steps) || steps.length === 0) {
        return null;
    }

    return (
        <div className="overflow-x-auto pb-2">
            <div className="flex min-w-[680px] items-start">
                {steps.map((step, index) => {
                    const state =
                        stateClasses[step.state]
                        ?? stateClasses.pending;

                    const StateIcon =
                        state.icon;

                    const isLast =
                        index === steps.length - 1;

                    const lineClass =
                        step.state === 'completed'
                            ? stateClasses.completed.line
                            : step.state === 'rejected'
                                ? stateClasses.rejected.line
                                : stateClasses.pending.line;

                    return (
                        <div
                            key={
                                step.key
                                ?? `${step.label}-${index}`
                            }
                            className="flex flex-1 items-start"
                        >
                            <div className="flex min-w-0 flex-1 flex-col items-center text-center">
                                <div
                                    className={[
                                        'flex h-10 w-10 items-center justify-center rounded-full border-2',
                                        state.circle,
                                    ].join(' ')}
                                >
                                    <StateIcon className="h-4 w-4" />
                                </div>

                                <p
                                    className={[
                                        'mt-3 text-xs font-bold',
                                        state.label,
                                    ].join(' ')}
                                >
                                    {step.label}
                                </p>

                                {step.description && (
                                    <p className="mt-1 max-w-28 text-[11px] leading-4 text-slate-400">
                                        {step.description}
                                    </p>
                                )}
                            </div>

                            {!isLast && (
                                <div
                                    className={[
                                        'mt-5 h-0.5 flex-1',
                                        lineClass,
                                    ].join(' ')}
                                />
                            )}
                        </div>
                    );
                })}
            </div>
        </div>
    );
}
