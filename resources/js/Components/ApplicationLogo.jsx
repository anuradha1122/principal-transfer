export default function ApplicationLogo({
    className = '',
    showText = false,
    textClassName = '',
}) {
    return (
        <div className="flex items-center gap-3">
            <img
                src="/images/logo.png"
                alt="Principal Transfer System Logo"
                className={[
                    'object-contain',
                    className,
                ].join(' ')}
            />

            {showText && (
                <div className={textClassName}>
                    <p className="font-bold">
                        Sipthathu Principal Transfer System
                    </p>

                    <p className="text-xs opacity-70">
                        Sabaragamuwa Province
                    </p>
                </div>
            )}
        </div>
    );
}
