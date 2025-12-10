<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión-CODENAME FreeOLT </title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #00c853 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            backdrop-filter: blur(10px);
        }
        .login-header {
            background: linear-gradient(135deg, #FFFF 0%, #FFFF 100%);
            border-radius: 15px 15px 0 0;
            color: black;
            padding: 2rem;
            text-align: center;
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .request-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        .request-link:hover {
            color: #764ba2;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card login-card">
                    <div class="login-header">
                        <img src="{{ asset('images/intersanpablo-logo.png') }}" alt="Intersan Pablo" style="height:125px; object-fit:contain;" class="mb-2">
                    </div>
                    <div class="card-body p-4">
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error:</strong> {{ $errors->first() }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autofocus
                                       placeholder="tu@email.com">
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       required
                                       placeholder="••••••••">
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Recordar sesión</label>
                            </div>

                            <button type="submit" class="btn btn-login text-white w-100 mb-3">
                                Iniciar Sesión
                            </button>

                            {{-- Enlace para solicitar registro --}}
                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    ¿Necesitas una cuenta? 
                                    <a href="#" class="request-link" data-bs-toggle="modal" data-bs-target="#requestRegisterModal">
                                        Solicitar registro
                                    </a>
                                </small>
                            </div>

                            <div class="text-center mt-2">
                                <small class="text-muted">
                                    INTERSANPABLO &copy; {{ date('Y') }}
                                </small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal para solicitud de registro --}}
    <div class="modal fade" id="requestRegisterModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">📧 Solicitar Cuenta - FreeOLT</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <small>
                            <strong>⚠️ Sistema de registro controlado</strong><br>
                            Tu solicitud será revisada por un administrador. 
                            Te notificaremos por email cuando tu cuenta sea activada.
                        </small>
                    </div>
                    
                    <form id="requestRegisterForm">
                        @csrf
                        <div class="mb-3">
                            <label for="request_name" class="form-label">Nombre completo *</label>
                            <input type="text" class="form-control" id="request_name" name="name" required
                                   placeholder="Ingresa tu nombre completo">
                        </div>
                        <div class="mb-3">
                            <label for="request_email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="request_email" name="email" required
                                   placeholder="tu@email.com">
                        </div>
                        <div class="mb-3">
                            <label for="request_phone" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="request_phone" name="phone"
                                   placeholder="+52 123 456 7890">
                        </div>
                        <div class="mb-3">
                            <label for="request_role" class="form-label">Rol solicitado *</label>
                            <select class="form-select" id="request_role" name="role" required>
                                <option value="">Selecciona un rol...</option>
                                <option value="technician">Técnico</option>
                                <option value="support">Soporte Técnico</option>
                                <option value="customer">Cliente</option>
                                <option value="read-only">Solo Lectura</option>
                            </select>
                            <small class="form-text text-muted">
                                El administrador asignará el rol final según tu perfil
                            </small>
                        </div>
                        <div class="mb-3">
                            <label for="request_notes" class="form-label">Justificación/Motivo *</label>
                            <textarea class="form-control" id="request_notes" name="notes" rows="3" 
                                      placeholder="Explica por qué necesitas acceso al sistema FreeOLT..."
                                      required></textarea>
                            <small class="form-text text-muted">
                                Esta información ayudará al administrador a evaluar tu solicitud
                            </small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="submitRequest()">
                        📨 Enviar Solicitud
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Efecto de focus en los inputs
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.classList.remove('focused');
                }
            });
        });

        // Función para enviar solicitud de registro
        function submitRequest() {
            const form = document.getElementById('requestRegisterForm');
            const formData = new FormData(form);
            const submitBtn = event.target;
            
            // Validación básica
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                alert('❌ Por favor completa todos los campos obligatorios.');
                return;
            }
            
            // Deshabilitar botón durante el envío
            submitBtn.disabled = true;
            submitBtn.innerHTML = '⏳ Enviando...';
            
            fetch('/register-request', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ Solicitud enviada correctamente. Un administrador la revisará pronto.');
                    // Cerrar modal y resetear formulario
                    const modal = bootstrap.Modal.getInstance(document.getElementById('requestRegisterModal'));
                    modal.hide();
                    form.reset();
                } else {
                    alert('❌ Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Error al enviar la solicitud. Por favor intenta nuevamente.');
            })
            .finally(() => {
                // Rehabilitar botón
                submitBtn.disabled = false;
                submitBtn.innerHTML = '📨 Enviar Solicitud';
            });
        }

        // Cerrar modal al hacer click fuera de él
        document.getElementById('requestRegisterModal').addEventListener('click', function(e) {
            if (e.target === this) {
                const modal = bootstrap.Modal.getInstance(this);
                modal.hide();
            }
        });
    </script>
</body>
</html>