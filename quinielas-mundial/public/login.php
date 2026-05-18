<?php
session_start();

if (isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <?php require_once __DIR__ . '/../app/views/layouts/header.php'; ?>
    <title>Quinielas Mundial 2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen flex items-center justify-center bg-[#0b1018] px-4 py-6 sm:py-8">

    <!-- ALERTAS DE ERROR -->
    <?php if (isset($_SESSION['error'])): ?>
        <div id="alertaError"
            class="fixed top-5 left-1/2 -translate-x-1/2 z-50
         bg-red-500/95 backdrop-blur-md text-white px-6 py-4 rounded-2xl
         shadow-2xl border border-red-300/20 transition-all duration-300">
            <span class="text-sm font-semibold">
                <?= $_SESSION['error'] ?>
            </span>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- 
      NOTA: El bloque de $_SESSION['success'] fue removido de aquí. 
      Debes ponerlo en tu index.php para que muestre el mensaje de bienvenida 
      al iniciar sesión automáticamente.
    -->

    <div class="login-shell relative w-full max-w-[880px] min-h-[560px] bg-white rounded-[30px] overflow-hidden shadow-2xl">

        <input
            type="checkbox"
            id="toggle"
            class="peer hidden"
            <?= (isset($_GET['panel']) && $_GET['panel'] === 'registro') ? 'checked' : '' ?>>

        <!-- GRID PRINCIPAL -->
        <div class="login-grid grid grid-cols-2 min-h-[560px]">

            <!-- REGISTRO (Se le añadió id="form-registro") -->
            <div class="register-panel flex items-center justify-center px-8 sm:px-10 lg:px-14 py-10">
                <form id="form-registro" action="../app/controllers/UsuarioController.php"
                    method="POST"
                    class="w-full max-w-[320px] flex flex-col gap-4">

                    <input type="hidden" name="accion" value="registro">

                    <h2 class="text-3xl font-bold text-gray-900 text-center mb-4">
                        Crear cuenta
                    </h2>

                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Nombre completo</label>
                        <input type="text" name="nombre" placeholder="Tu nombre"
                            class="w-full px-4 py-3 rounded-xl bg-gray-100 outline-none focus:ring-2 focus:ring-yellow-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Usuario</label>
                        <input type="text" name="usuario" placeholder="Tu usuario"
                            class="w-full px-4 py-3 rounded-xl bg-gray-100 outline-none focus:ring-2 focus:ring-yellow-500">
                    </div>

                    <!-- CONTRASEÑA -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">
                            Contraseña
                        </label>

                        <div class="relative">
                            <input type="password"
                                id="registerPassword"
                                name="password"
                                placeholder="Tu contraseña"
                                minlength="6"
                                pattern="(?=.*[A-Za-zÁÉÍÓÚÜÑáéíóúüñ])(?=.*\d).{6,}"
                                title="Mínimo 6 caracteres, una letra y un número"
                                class="w-full px-4 py-3 pr-12 rounded-xl bg-gray-100 outline-none focus:ring-2 focus:ring-yellow-500">

                            <button type="button"
                                onclick="togglePassword('registerPassword', 'registerEye')"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-black transition">
                                <span id="registerEye">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-eye-off">
                                        <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20C7 20 2.73 16.11 1 12c.73-1.61 1.83-3.08 3.21-4.31" />
                                        <path d="M9.9 4.24A10.94 10.94 0 0 1 12 4c5 0 9.27 3.89 11 8a11.05 11.05 0 0 1-4.08 5.19" />
                                        <path d="M1 1l22 22" />
                                        <path d="M10.58 10.58a2 2 0 0 0 2.83 2.83" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- REPETIR CONTRASEÑA -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">
                            Repetir contraseña
                        </label>

                        <div class="relative">
                            <input type="password"
                                id="confirmPassword"
                                name="confirm_password"
                                placeholder="Repite tu contraseña"
                                class="w-full px-4 py-3 pr-12 rounded-xl bg-gray-100 outline-none focus:ring-2 focus:ring-yellow-500">

                            <button type="button"
                                onclick="togglePassword('confirmPassword', 'confirmEye')"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-black transition">
                                <span id="confirmEye">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-eye-off">
                                        <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20C7 20 2.73 16.11 1 12c.73-1.61 1.83-3.08 3.21-4.31" />
                                        <path d="M9.9 4.24A10.94 10.94 0 0 1 12 4c5 0 9.27 3.89 11 8a11.05 11.05 0 0 1-4.08 5.19" />
                                        <path d="M1 1l22 22" />
                                        <path d="M10.58 10.58a2 2 0 0 0 2.83 2.83" />
                                    </svg>
                                </span>
                            </button>
                        </div>

                        <!-- MENSAJE ERROR -->
                        <p id="passwordError"
                            class="hidden text-red-500 text-sm font-semibold mt-2">
                            Las contraseñas no coinciden
                        </p>
                        <p id="passwordRulesError"
                            class="hidden text-red-500 text-sm font-semibold mt-2">
                            La contraseña debe tener mínimo 6 caracteres, una letra y un número
                        </p>
                    </div>

                    <button
                        class="mt-3 w-full py-3 rounded-xl
                    bg-gradient-to-r from-[#c89b3c] via-[#f7d774] to-[#b8860b]
                    text-black font-bold tracking-wide shadow-lg shadow-yellow-700/30
                    hover:scale-[1.03] active:scale-95 transition-all duration-150">
                        Registrarme
                    </button>
                </form>
            </div>

            <!-- LOGIN -->
            <div class="login-panel flex items-center justify-center px-8 sm:px-10 lg:px-14 py-10">
                <form action="../app/controllers/UsuarioController.php"
                    method="POST"
                    class="w-full max-w-[320px] flex flex-col gap-4">

                    <input type="hidden" name="accion" value="login">

                    <h2 class="text-3xl font-bold text-gray-900 text-center mb-4">
                        Iniciar sesión
                    </h2>

                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Usuario</label>
                        <input type="text" name="usuario" placeholder="Ingresa tu usuario"
                            class="w-full px-4 py-3 rounded-xl bg-gray-100 outline-none focus:ring-2 focus:ring-yellow-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">
                            Contraseña
                        </label>

                        <div class="relative">
                            <input type="password"
                                id="loginPassword"
                                name="password"
                                placeholder="Ingresa tu contraseña"
                                class="w-full px-4 py-3 pr-12 rounded-xl bg-gray-100 outline-none focus:ring-2 focus:ring-yellow-500">

                            <button type="button"
                                onclick="togglePassword('loginPassword', 'loginEye')"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-black transition">
                                <span id="loginEye">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-eye-off">
                                        <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20C7 20 2.73 16.11 1 12c.73-1.61 1.83-3.08 3.21-4.31" />
                                        <path d="M9.9 4.24A10.94 10.94 0 0 1 12 4c5 0 9.27 3.89 11 8a11.05 11.05 0 0 1-4.08 5.19" />
                                        <path d="M1 1l22 22" />
                                        <path d="M10.58 10.58a2 2 0 0 0 2.83 2.83" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>

                    <button
                        class="mt-3 w-full py-3 rounded-xl
                    bg-gradient-to-r from-[#c89b3c] via-[#f7d774] to-[#b8860b]
                    text-black font-bold tracking-wide shadow-lg shadow-yellow-700/30
                    hover:scale-[1.03] active:scale-95 transition-all duration-150">
                        Entrar
                    </button>
                </form>
            </div>
        </div>

        <!-- PANEL ANIMADO -->
        <div class="login-hero-panel absolute top-0 left-0 w-1/2 h-full bg-black rounded-r-[30px]
                flex flex-col items-center justify-center gap-8 px-10 text-center text-white
                transition-all duration-700
                peer-checked:translate-x-full peer-checked:rounded-l-[30px]">

            <img src="assets/img/copa26.jpeg" class="w-64 drop-shadow-2xl">

            <div class="space-y-3">
                <h2 class="text-4xl font-bold">Quiniela Mundial</h2>
                <p class="text-white/80 text-sm">Vive toda la pasión del Mundial 2026</p>
            </div>

            <label for="toggle"
                class="px-10 py-3 border border-white/70 rounded-full text-sm font-semibold cursor-pointer
               hover:bg-yellow-400/20 active:scale-95 transition z-50">
                <span class="login-text">CREAR CUENTA</span>
                <span class="register-text hidden">INICIAR SESIÓN</span>
            </label>
        </div>
    </div>

    <script>
        const alertaError = document.getElementById('alertaError');

        function ocultarAlerta(alerta) {
            if (alerta) {
                setTimeout(() => {
                    alerta.style.opacity = '0';
                    alerta.style.transform = 'translate(-50%, -10px)';

                    setTimeout(() => {
                        alerta.remove();
                    }, 300);
                }, 3000);
            }
        }

        ocultarAlerta(alertaError);

        const toggle = document.getElementById('toggle');
        const loginText = document.querySelector('.login-text');
        const registerText = document.querySelector('.register-text');

        function actualizarTextoBoton() {
            if (toggle.checked) {
                loginText.classList.add('hidden');
                registerText.classList.remove('hidden');
            } else {
                registerText.classList.add('hidden');
                loginText.classList.remove('hidden');
            }
        }

        actualizarTextoBoton();
        toggle.addEventListener('change', actualizarTextoBoton);

        const eyeOpen = `
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-eye-icon lucide-eye">
                <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/>
                <circle cx="12" cy="12" r="3"/>
            </svg>
        `;

        const eyeClosed = `
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-eye-off">
                <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20C7 20 2.73 16.11 1 12c.73-1.61 1.83-3.08 3.21-4.31"/>
                <path d="M9.9 4.24A10.94 10.94 0 0 1 12 4c5 0 9.27 3.89 11 8a11.05 11.05 0 0 1-4.08 5.19"/>
                <path d="M1 1l22 22"/>
                <path d="M10.58 10.58a2 2 0 0 0 2.83 2.83"/>
            </svg>
        `;

        function togglePassword(inputId, eyeId) {
            const input = document.getElementById(inputId);
            const eye = document.getElementById(eyeId);

            if (input.type === 'password') {
                input.type = 'text';
                eye.innerHTML = eyeOpen;
            } else {
                input.type = 'password';
                eye.innerHTML = eyeClosed;
            }
        }

        /* CAMBIO AQUÍ: Ahora se selecciona de forma segura por ID */
        const registerForm = document.getElementById('form-registro');

        const passwordInput = document.getElementById('registerPassword');
        const confirmInput = document.getElementById('confirmPassword');
        const passwordError = document.getElementById('passwordError');
        const passwordRulesError = document.getElementById('passwordRulesError');

        function passwordCumpleReglas() {
            return passwordInput.value.length >= 6 &&
                /[A-Za-zÁÉÍÓÚÜÑáéíóúüñ]/.test(passwordInput.value) &&
                /\d/.test(passwordInput.value);
        }

        function validarPasswords() {
            if (passwordInput.value === '') {
                passwordRulesError.classList.add('hidden');
                passwordInput.classList.remove('ring-2', 'ring-red-500');
            } else if (!passwordCumpleReglas()) {
                passwordRulesError.classList.remove('hidden');
                passwordInput.classList.add('ring-2', 'ring-red-500');
            } else {
                passwordRulesError.classList.add('hidden');
                passwordInput.classList.remove('ring-2', 'ring-red-500');
            }

            if (confirmInput.value === '') {
                passwordError.classList.add('hidden');
                confirmInput.classList.remove('ring-2', 'ring-red-500');
                return;
            }

            if (passwordInput.value !== confirmInput.value) {
                passwordError.classList.remove('hidden');
                confirmInput.classList.add('ring-2', 'ring-red-500');
            } else {
                passwordError.classList.add('hidden');
                confirmInput.classList.remove('ring-2', 'ring-red-500');
            }
        }

        passwordInput.addEventListener('input', validarPasswords);
        confirmInput.addEventListener('input', validarPasswords);

        registerForm.addEventListener('submit', function(e) {
            if (!passwordCumpleReglas()) {
                e.preventDefault();
                passwordRulesError.classList.remove('hidden');
                passwordInput.classList.add('ring-2', 'ring-red-500');
            }

            if (passwordInput.value !== confirmInput.value) {
                e.preventDefault();
                passwordError.classList.remove('hidden');
                confirmInput.classList.add('ring-2', 'ring-red-500');
            }
        });
    </script>
</body>

</html>
