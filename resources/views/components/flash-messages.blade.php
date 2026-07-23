@php
    $notifications = [];
    if ($msg = session('success')) $notifications[] = ['type' => 'success', 'message' => $msg];
    if ($msg = session('error'))   $notifications[] = ['type' => 'error', 'message' => $msg];
    if ($msg = session('warning')) $notifications[] = ['type' => 'warning', 'message' => $msg];
    if ($msg = session('info'))    $notifications[] = ['type' => 'info', 'message' => $msg];
@endphp

@if(count($notifications) > 0 || $errors->any())
    <div
        x-data="{
            toasts: {{ json_encode($notifications) }},
            errors: {{ json_encode($errors->any() ? ['message' => $errors->first(), 'all' => $errors->all()] : []) }},
            showErrors: {{ $errors->any() ? 'true' : 'false' }},
            init() {
                this.toasts.forEach((_, i) => {
                    setTimeout(() => this.dismiss(i), 5000 + (i * 500));
                });
                if (this.showErrors) {
                    setTimeout(() => this.dismissErrors(), 8000);
                }
            },
            dismiss(index) {
                if (this.toasts[index]) {
                    this.toasts[index].removing = true;
                    setTimeout(() => {
                        this.toasts.splice(index, 1);
                    }, 300);
                }
            },
            dismissErrors() {
                this.showErrors = false;
            },
            icon(type) {
                const icons = {
                    success: '<path fill-rule=\"evenodd\" d=\"M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z\" clip-rule=\"evenodd\"/>',
                    error: '<path fill-rule=\"evenodd\" d=\"M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z\" clip-rule=\"evenodd\"/>',
                    warning: '<path fill-rule=\"evenodd\" d=\"M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z\" clip-rule=\"evenodd\"/>',
                    info: '<path fill-rule=\"evenodd\" d=\"M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z\" clip-rule=\"evenodd\"/>'
                };
                return icons[type] || icons.info;
            },
            colorClasses(type) {
                const colors = {
                    success: 'border-green-400 bg-green-50 text-green-800',
                    error: 'border-red-400 bg-red-50 text-red-800',
                    warning: 'border-amber-400 bg-amber-50 text-amber-800',
                    info: 'border-blue-400 bg-blue-50 text-blue-800'
                };
                return colors[type] || colors.info;
            },
            progressBarClass(type) {
                const colors = {
                    success: 'bg-green-500',
                    error: 'bg-red-500',
                    warning: 'bg-amber-500',
                    info: 'bg-blue-500'
                };
                return colors[type] || colors.info;
            },
            iconColorClass(type) {
                const colors = {
                    success: 'text-green-500',
                    error: 'text-red-500',
                    warning: 'text-amber-500',
                    info: 'text-blue-500'
                };
                return colors[type] || colors.info;
            }
        }"
        class="fixed top-4 right-4 z-50 flex flex-col gap-3 w-full max-w-sm pointer-events-none"
    >
        <template x-for="(toast, index) in toasts" :key="index">
            <div
                x-show="!toast.removing"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-8"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 translate-x-8"
                class="pointer-events-auto rounded-xl border-l-4 shadow-lg overflow-hidden"
                :class="colorClasses(toast.type)"
            >
                <div class="flex items-start gap-3 p-4">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20" x-html="icon(toast.type)"></svg>
                    <span class="text-sm font-medium flex-1" x-text="toast.message"></span>
                    <button
                        @click="dismiss(index)"
                        class="flex-shrink-0 opacity-60 hover:opacity-100 transition-opacity"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="h-1 w-full bg-black/5">
                    <div
                        class="h-full rounded-full"
                        :class="progressBarClass(toast.type)"
                        x-init="$el.style.width = '100%'; $el.style.transition = 'width 5s linear'; requestAnimationFrame(() => { $el.style.width = '0%'; })"
                    ></div>
                </div>
            </div>
        </template>

        <div
            x-show="showErrors"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-8"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 translate-x-8"
            class="pointer-events-auto rounded-xl border-l-4 border-red-400 bg-red-50 text-red-800 shadow-lg overflow-hidden"
        >
            <div class="flex items-start gap-3 p-4">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-semibold mb-1">Please fix the following errors:</p>
                    <ul class="list-disc list-inside text-sm space-y-0.5">
                        <template x-for="(err, i) in (errors.all || [])" :key="i">
                            <li x-text="err"></li>
                        </template>
                    </ul>
                </div>
                <button @click="dismissErrors()" class="flex-shrink-0 opacity-60 hover:opacity-100 transition-opacity">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="h-1 w-full bg-black/5">
                <div
                    class="h-full bg-red-500 rounded-full"
                    x-init="$el.style.width = '100%'; $el.style.transition = 'width 8s linear'; requestAnimationFrame(() => { $el.style.width = '0%'; })"
                ></div>
            </div>
        </div>
    </div>
@endif
