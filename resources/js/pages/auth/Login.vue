<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Inicia sesión en tu cuenta',
        description:
            'Ingresa tu correo y contraseña para acceder al panel de administración.',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <Head title="Iniciar sesión" />

    <div
        v-if="status"
        class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-center text-sm font-medium text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300"
    >
        {{ status }}
    </div>

    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-5"
    >
        <div class="grid gap-5">
            <div class="grid gap-2">
                <Label
                    for="email"
                    class="text-sm font-semibold text-[#042163] dark:text-foreground"
                >
                    Correo electrónico
                </Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    required
                    v-focus
                    :tabindex="1"
                    autocomplete="email"
                    placeholder="correo@ejemplo.com"
                    class="h-11 border-slate-300 bg-white focus-visible:border-[#042163] focus-visible:ring-[#042163]/20 dark:border-input dark:bg-input/30"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <div
                    class="flex flex-col items-start gap-1 sm:flex-row sm:items-center sm:justify-between"
                >
                    <Label
                        for="password"
                        class="text-sm font-semibold text-[#042163] dark:text-foreground"
                    >
                        Contraseña
                    </Label>
                    <Link
                        v-if="canResetPassword"
                        :href="request()"
                        class="text-xs font-semibold text-[#c63427] underline-offset-4 hover:underline"
                        :tabindex="5"
                    >
                        ¿Olvidaste tu contraseña?
                    </Link>
                </div>
                <PasswordInput
                    id="password"
                    name="password"
                    required
                    :tabindex="2"
                    autocomplete="current-password"
                    placeholder="Tu contraseña"
                    class="h-11 border-slate-300 bg-white focus-visible:border-[#042163] focus-visible:ring-[#042163]/20 dark:border-input dark:bg-input/30"
                />
                <InputError :message="errors.password" />
            </div>

            <Label
                for="remember"
                class="flex items-center gap-3 text-sm font-medium text-slate-600 dark:text-muted-foreground"
            >
                <Checkbox
                    id="remember"
                    name="remember"
                    :tabindex="3"
                    class="border-slate-300 data-[state=checked]:border-[#042163] data-[state=checked]:bg-[#042163] dark:border-input"
                />
                <span>Recordarme</span>
            </Label>

            <Button
                type="submit"
                class="mt-1 h-11 w-full bg-[#042163] text-sm font-semibold text-white shadow-lg shadow-[#042163]/20 transition-colors hover:bg-[#0a3a9c] focus-visible:ring-[#042163]/30"
                :tabindex="4"
                :disabled="processing"
                data-test="login-button"
            >
                <Spinner v-if="processing" />
                Iniciar sesión
            </Button>
        </div>
    </Form>

    <p
        class="mt-6 text-center text-xs text-slate-500 dark:text-muted-foreground"
    >
        ¿Problemas para acceder? Llámanos al
        <a
            href="tel:+523322544431"
            class="font-semibold text-[#042163] hover:text-[#c63427] dark:text-foreground"
        >
            33 2254 4431
        </a>
    </p>
</template>
