@extends('layouts.app')

@section('styles')
    <style>
        .interactive-card-preview {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            border-radius: 18px;
            min-height: 230px;
            color: #ffffff;
            box-shadow: 0 16px 36px -4px rgba(15, 23, 42, 0.35);
            padding: 1.75rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .interactive-card-preview::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.25) 0%, transparent 70%);
            pointer-events: none;
        }

        .card-chip {
            width: 46px;
            height: 34px;
            border-radius: 8px;
            background: linear-gradient(135deg, #fcd34d 0%, #d97706 100%);
            box-shadow: inset 0 1px 2px rgba(255, 255, 255, 0.4);
            border: 1px solid rgba(0, 0, 0, 0.15);
        }

        .card-number-display {
            font-family: 'Courier New', Courier, monospace;
            font-size: 1.35rem;
            letter-spacing: 2.5px;
            font-weight: 700;
            color: #ffffff;
            text-shadow: 0 2px 4px rgba(0,0,0,0.4);
        }

        .card-mini-wallet {
            background: #ffffff;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-subtle);
            box-shadow: var(--shadow-card);
            transition: all 0.25s ease;
            overflow: hidden;
        }

        .card-mini-wallet:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-card-hover);
        }
    </style>
@endsection

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <!-- Page Hero Banner -->
        <div class="page-hero-banner">
            <div>
                <h1 class="page-hero-title">Mis Métodos de Pago 💳</h1>
                <p class="page-hero-subtitle">Gestiona de forma segura tus tarjetas para la suscripción a cursos.</p>
            </div>
            <div>
                <span class="badge-modern-primary">
                    <i class="bi bi-shield-lock-fill me-1"></i> Entorno Encriptado SSL
                </span>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success d-flex align-items-center gap-3 p-4 rounded-4 shadow-sm mb-6 border-0 bg-light-success" id="flashAlert">
                <i class="bi bi-check-circle-fill text-success fs-3"></i>
                <div>
                    <h5 class="fw-bold text-success mb-0">Operación Exitosa</h5>
                    <span class="text-success fs-7">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger p-4 rounded-4 shadow-sm mb-6 border-0" id="flashAlert">
                <div class="fw-bold fs-6 mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Por favor corrige los siguientes errores:</div>
                <ul class="mb-0 ps-4 fs-7">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Card Registration Form Card -->
        <div class="modern-card mb-8">
            <div class="modern-card-header">
                <div class="d-flex align-items-center gap-2">
                    <div class="user-avatar-circle" style="width: 36px; height: 36px; font-size: 1rem;">
                        <i class="bi bi-credit-card-fill"></i>
                    </div>
                    <div>
                        <h3 class="modern-card-title" id="formTitle">Registrar Nueva Tarjeta</h3>
                        <span class="text-muted fs-8">Tus datos bancarios están protegidos bajo estándares de seguridad.</span>
                    </div>
                </div>
            </div>

            <div class="p-4 p-md-6">
                <form id="cardForm" action="{{ route('registrar.tarjeta') }}" method="POST" novalidate>
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="card_id" id="cardId" value="">

                    <div class="row g-6 align-items-center">
                        <!-- Inputs Column -->
                        <div class="col-lg-7">
                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="form-label-modern" for="numero_tarjeta">Número de tarjeta <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-credit-card text-muted"></i></span>
                                        <input type="text" id="numero_tarjeta" name="numero_tarjeta" class="form-control form-control-modern border-start-0" 
                                               maxlength="23" inputmode="numeric" autocomplete="cc-number" placeholder="4532 0123 4567 8901" required>
                                    </div>
                                    <small class="text-muted fs-8 mt-1 d-block">Acepta tarjetas de débito o crédito (13 a 19 dígitos).</small>
                                </div>

                                <div class="col-6">
                                    <label class="form-label-modern" for="expiracion">Expiración <span class="text-danger">*</span></label>
                                    <input type="text" id="expiracion" name="expiracion" class="form-control form-control-modern" 
                                           placeholder="MM/AA" maxlength="5" inputmode="numeric" required>
                                </div>

                                <div class="col-6">
                                    <label class="form-label-modern" for="cvv">CVV <span class="text-danger">*</span></label>
                                    <input type="password" id="cvv" name="cvv" class="form-control form-control-modern" 
                                           placeholder="•••" maxlength="4" inputmode="numeric" required>
                                </div>

                                <div class="col-12">
                                    <label class="form-label-modern" for="banco">Institución Bancaria <span class="text-danger">*</span></label>
                                    <select id="banco" name="banco" class="form-select form-select-modern" required>
                                        <option value="">-- Seleccionar Banco --</option>
                                        <option value="BVVA">BBVA México</option>
                                        <option value="BANCOAZTECA">Banco Azteca</option>
                                        <option value="BANAMEX">Citibanamex</option>
                                        <option value="BANORTE">Banorte</option>
                                    </select>
                                </div>

                                <div class="col-12 d-flex gap-3 pt-2">
                                    <button type="submit" class="btn-modern-primary" id="submitButton">
                                        <i class="bi bi-check2-circle"></i>
                                        <span>Guardar Tarjeta</span>
                                    </button>
                                    <button type="button" class="btn-modern-outline" id="cancelEditButton" style="display:none;">
                                        <i class="bi bi-x-circle"></i>
                                        <span>Cancelar Edición</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Live Card Simulator Preview Column -->
                        <div class="col-lg-5">
                            <div class="interactive-card-preview">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="card-chip"></div>
                                    <div class="fw-bold text-white fs-6" id="previewBank">
                                        <i class="bi bi-bank me-1"></i> Banco
                                    </div>
                                </div>

                                <div class="my-3">
                                    <div class="text-white-50 fs-8 text-uppercase fw-semibold mb-1">Número de Cuenta</div>
                                    <div class="card-number-display" id="previewNumber">•••• •••• •••• ••••</div>
                                </div>

                                <div class="d-flex justify-content-between align-items-end">
                                    <div>
                                        <div class="text-white-50 fs-8 text-uppercase fw-semibold">Titular</div>
                                        <div class="fw-bold text-white fs-7">{{ Auth::user()->name ?? 'Titular' }}</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="text-white-50 fs-8 text-uppercase fw-semibold">Vencimiento</div>
                                        <div class="fw-bold text-white fs-7" id="previewExpiry">MM/AA</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Registered Cards Section -->
        @php
            $cards = $cards ?? [];
        @endphp

        <div class="d-flex align-items-center justify-content-between mb-4">
            <h2 class="fs-4 fw-bold text-dark mb-0">Tarjetas Registradas ({{ count($cards) }})</h2>
            <span class="text-muted fs-7">Disponibles para pago automático en cursos.</span>
        </div>

        <div class="row g-4">
            @forelse($cards as $card)
                <div class="col-md-6 col-xl-4">
                    <div class="card-mini-wallet p-4">
                        <div class="d-flex align-items-start justify-content-between mb-4">
                            <div class="d-flex align-items-center gap-2">
                                <div class="user-avatar-circle" style="width: 36px; height: 36px; font-size: 0.9rem;">
                                    <i class="bi bi-credit-card"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark fs-6">{{ $card['type'] ?? 'Tarjeta' }}</div>
                                    <small class="text-muted">Débito / Crédito</small>
                                </div>
                            </div>
                            <span class="badge-modern-success">Activa</span>
                        </div>

                        <div class="card-number-display text-dark fs-5 mb-3" style="color: #1e293b !important; text-shadow: none;">
                            •••• •••• •••• {{ $card['last4'] }}
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top border-light-subtle">
                            <div>
                                <span class="text-muted fs-8 d-block">Vence:</span>
                                <strong class="text-dark fs-7">{{ $card['expiry'] }}</strong>
                            </div>
                            <button type="button" class="btn btn-sm btn-light-primary px-3 py-1 fw-bold edit-card-btn" data-id="{{ $card['id'] ?? '' }}">
                                <i class="bi bi-pencil-square me-1"></i> Editar
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="modern-card text-center py-10">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle p-4 mb-3" style="background: #f1f5f9;">
                            <i class="bi bi-credit-card-2-front fs-2x text-muted"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-1">Aún no tienes tarjetas registradas</h4>
                        <p class="text-muted fs-7 mb-0">Completa el formulario superior para añadir tu primer método de pago.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('cardForm');
            const formTitle = document.getElementById('formTitle');
            const submitButton = document.getElementById('submitButton');
            const cancelEditButton = document.getElementById('cancelEditButton');
            const formMethod = document.getElementById('formMethod');
            const cardIdInput = document.getElementById('cardId');
            const numberInput = document.getElementById('numero_tarjeta');
            const expiryInput = document.getElementById('expiracion');
            const cvvInput = document.getElementById('cvv');
            const bankSelect = document.getElementById('banco');
            const previewNumber = document.getElementById('previewNumber');
            const previewExpiry = document.getElementById('previewExpiry');
            const previewBank = document.getElementById('previewBank');

            const setFieldState = (field, isValid) => {
                field.classList.remove('is-valid', 'is-invalid');
                field.classList.add(isValid ? 'is-valid' : 'is-invalid');
            };

            const formatCardNumber = (value) => {
                const digits = value.replace(/\D/g, '').slice(0, 19);
                return digits.replace(/(.{4})/g, '$1 ').trim();
            };

            const resetForm = () => {
                form.reset();
                form.action = '{{ route('registrar.tarjeta') }}';
                formMethod.value = 'POST';
                cardIdInput.value = '';
                formTitle.textContent = 'Registrar Nueva Tarjeta';
                submitButton.innerHTML = '<i class="bi bi-check2-circle"></i><span>Guardar Tarjeta</span>';
                cancelEditButton.style.display = 'none';
                [numberInput, expiryInput, cvvInput].forEach((field) => {
                    field.classList.remove('is-valid', 'is-invalid');
                });
                bankSelect.classList.remove('is-valid', 'is-invalid');
                updatePreview();
            };

            const updatePreview = () => {
                const numberValue = numberInput.value.replace(/\s/g, '');
                previewNumber.textContent = numberValue ? formatCardNumber(numberInput.value) : '•••• •••• •••• ••••';
                previewExpiry.textContent = expiryInput.value || 'MM/AA';
                previewBank.innerHTML = '<i class="bi bi-bank me-1"></i> ' + (bankSelect.value || 'Banco');
            };

            numberInput.addEventListener('input', function () {
                const formatted = formatCardNumber(this.value);
                this.value = formatted;
                const isValid = /^\d{13,19}$/.test(this.value.replace(/\s/g, ''));
                setFieldState(this, isValid);
                updatePreview();
            });

            expiryInput.addEventListener('input', function () {
                let value = this.value.replace(/\D/g, '').slice(0, 4);
                if (value.length > 2) {
                    value = value.slice(0, 2) + '/' + value.slice(2);
                }
                this.value = value;
                const isValid = /^(0[1-9]|1[0-2])\/\d{2}$/.test(value);
                setFieldState(this, isValid);
                updatePreview();
            });

            cvvInput.addEventListener('input', function () {
                this.value = this.value.replace(/\D/g, '').slice(0, 4);
                const isValid = /^\d{3,4}$/.test(this.value);
                setFieldState(this, isValid);
            });

            bankSelect.addEventListener('change', function () {
                this.classList.remove('is-valid', 'is-invalid');
                this.classList.add(this.value ? 'is-valid' : 'is-invalid');
                updatePreview();
            });

            form.addEventListener('submit', function (event) {
                const numberValid = /^\d{13,19}$/.test(numberInput.value.replace(/\s/g, ''));
                const expiryValid = /^(0[1-9]|1[0-2])\/\d{2}$/.test(expiryInput.value);
                const cvvValid = /^\d{3,4}$/.test(cvvInput.value);
                const bankValid = !!bankSelect.value;

                setFieldState(numberInput, numberValid);
                setFieldState(expiryInput, expiryValid);
                setFieldState(cvvInput, cvvValid);
                bankSelect.classList.remove('is-valid', 'is-invalid');
                bankSelect.classList.add(bankValid ? 'is-valid' : 'is-invalid');

                if (!numberValid || !expiryValid || !cvvValid || !bankValid) {
                    event.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Datos incompletos',
                        text: 'Completa correctamente todos los campos de la tarjeta.',
                        confirmButtonColor: '#2563eb'
                    });
                }
            });

            document.querySelectorAll('.edit-card-btn').forEach((button) => {
                button.addEventListener('click', function () {
                    const cardId = this.dataset.id;
                    if (!cardId) return;

                    fetch('{{ url('/tarjeta') }}/' + cardId)
                        .then((response) => response.json())
                        .then((data) => {
                            form.action = '{{ url('/tarjeta') }}/' + data.id;
                            formMethod.value = 'PUT';
                            cardIdInput.value = data.id;
                            numberInput.value = formatCardNumber(data.numero_tarjeta || '');
                            expiryInput.value = data.expiracion || '';
                            cvvInput.value = data.cvv || '';
                            bankSelect.value = data.banco || '';
                            formTitle.textContent = 'Editar Tarjeta';
                            submitButton.innerHTML = '<i class="bi bi-pencil-square"></i><span>Actualizar Tarjeta</span>';
                            cancelEditButton.style.display = 'inline-flex';
                            [numberInput, expiryInput, cvvInput].forEach((field) => {
                                field.classList.remove('is-valid', 'is-invalid');
                            });
                            bankSelect.classList.remove('is-valid', 'is-invalid');
                            updatePreview();
                            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        });
                });
            });

            cancelEditButton.addEventListener('click', resetForm);
        });
    </script>
@endsection
