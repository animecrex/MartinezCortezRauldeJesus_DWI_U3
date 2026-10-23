@extends('layouts.app')

@section('styles')
    <style>
        .card-payment {
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
        }

        .card-payment .card-body {
            padding: 1.5rem;
        }

        .card-payment .card-footer {
            background-color: transparent;
            border-top: 1px solid #f1f3f5;
        }

        .card-preview {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            border-radius: 1rem;
            min-height: 220px;
            color: #fff;
            box-shadow: 0 12px 35px rgba(15, 23, 42, 0.2);
        }

        .card-preview .chip {
            width: 48px;
            height: 36px;
            border-radius: 8px;
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
        }

        .form-control.is-valid,
        .form-select.is-valid {
            border-color: #10b981;
            box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.15);
        }

        .form-control.is-invalid,
        .form-select.is-invalid {
            border-color: #ef4444;
            box-shadow: 0 0 0 0.2rem rgba(239, 68, 68, 0.15);
        }
    </style>
@endsection

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        Mis tarjetas</h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="#" class="text-muted text-hover-primary">Perfil</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">Tarjetas</li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                @if (session('success'))
                    <div class="alert alert-success mb-6" id="flashAlert">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger mb-6" id="flashAlert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card shadow-sm mb-6">
                    <div class="card-body">
                        <h3 class="fw-bold text-gray-900 mb-5" id="formTitle">Registrar tarjeta</h3>
                        <form id="cardForm" action="{{ route('registrar.tarjeta') }}" method="POST" novalidate>
                            @csrf
                            <input type="hidden" name="_method" id="formMethod" value="POST">
                            <input type="hidden" name="card_id" id="cardId" value="">
                            <div class="row g-4">
                                <div class="col-lg-7">
                                    <div class="row g-4">
                                        <div class="col-md-12">
                                            <label class="form-label">Número de tarjeta</label>
                                            <input type="text" id="numero_tarjeta" name="numero_tarjeta" class="form-control" maxlength="23" inputmode="numeric" autocomplete="cc-number" required>
                                            <div class="form-text">Ingresa entre 19 dígitos.</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Expiración</label>
                                            <input type="text" id="expiracion" name="expiracion" class="form-control" placeholder="MM/YY" maxlength="5" inputmode="numeric" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">CVV</label>
                                            <input type="text" id="cvv" name="cvv" class="form-control" maxlength="3" inputmode="numeric" required>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label">Banco</label>
                                            <select id="banco" name="banco" class="form-select" required>
                                                <option value="">Selecciona un banco</option>
                                                <option value="BVVA">BVVA</option>
                                                <option value="BANCOAZTECA">BANCOAZTECA</option>
                                                <option value="BANAMEX">BANAMEX</option>
                                                <option value="BANORTE">BANORTE</option>
                                            </select>
                                        </div>
                                        <div class="col-12 d-flex gap-2">
                                            <button type="submit" class="btn btn-primary" id="submitButton">Guardar tarjeta</button>
                                            <button type="button" class="btn btn-secondary" id="cancelEditButton" style="display:none;">Cancelar edición</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-5">
                                    <div class="card-preview p-5 d-flex flex-column justify-content-between">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="chip"></div>
                                            <div class="fw-semibold text-white-50" id="previewBank">Banco</div>
                                        </div>
                                        <div>
                                            <div class="fs-6 text-white-50 mb-2">Número</div>
                                            <div class="fs-4 fw-bold mb-4" id="previewNumber">•••• •••• •••• ••••</div>
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <div class="fs-8 text-white-50">Titular</div>
                                                    <div class="fw-semibold">Titular</div>
                                                </div>
                                                <div>
                                                    <div class="fs-8 text-white-50">Vence</div>
                                                    <div class="fw-semibold" id="previewExpiry">MM/YY</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                @php
                    $cards = $cards ?? [];
                @endphp

                <div class="row g-6">
                    @forelse($cards as $card)
                        <div class="col-xl-6">
                            <div class="card card-payment shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start mb-8">
                                        <div>
                                            <div class="fs-4 fw-bold text-gray-900 mb-1">{{ $card['type'] ?? 'Tarjeta' }}</div>
                                            <div class="text-gray-600">•••• {{ $card['last4'] }}</div>
                                        </div>
                                        <span class="badge badge-light-success fw-bold fs-8 px-3 py-2">{{ $card['type'] ?? 'N/A' }}</span>
                                    </div>

                                    <div class="row gy-5">
                                        <div class="col-12 col-sm-6">
                                            <div class="fw-semibold text-gray-700 fs-7 mb-2">Nombre del titular</div>
                                            <div class="text-gray-900">{{ $card['holder'] }}</div>
                                        </div>
                                        <div class="col-6 col-sm-3">
                                            <div class="fw-semibold text-gray-700 fs-7 mb-2">Vence</div>
                                            <div class="text-gray-900">{{ $card['expiry'] }}</div>
                                        </div>
                                        <div class="col-6 col-sm-3">
                                            <div class="fw-semibold text-gray-700 fs-7 mb-2">Últimos 4</div>
                                            <div class="text-gray-900">{{ $card['last4'] }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer d-flex justify-content-end gap-2 py-4">
                                    <button type="button" class="btn btn-light btn-sm edit-card-btn" data-id="{{ $card['id'] ?? '' }}">Editar</button>
                                    <a href="#" class="btn btn-danger btn-sm">Eliminar</a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-body text-center py-10">
                                    <h3 class="text-gray-900 fw-bold mb-3">Aún no tienes tarjetas registradas</h3>
                                    <p class="text-gray-600 mb-6">Agrega una tarjeta para gestionar tus métodos de pago.</p>
                                    <a href="#" class="btn btn-primary">Agregar tarjeta</a>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
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
                formTitle.textContent = 'Registrar tarjeta';
                submitButton.textContent = 'Guardar tarjeta';
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
                previewExpiry.textContent = expiryInput.value || 'MM/YY';
                previewBank.textContent = bankSelect.value || 'Banco';
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
                    const alertBox = document.createElement('div');
                    alertBox.className = 'alert alert-danger mt-4';
                    alertBox.textContent = 'Completa correctamente todos los campos para guardar la tarjeta.';
                    form.insertBefore(alertBox, form.firstChild);
                    setTimeout(() => alertBox.remove(), 4000);
                }
            });

            document.querySelectorAll('.edit-card-btn').forEach((button) => {
                button.addEventListener('click', function () {
                    const cardId = this.dataset.id;
                    if (!cardId) {
                        return;
                    }

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
                            formTitle.textContent = 'Editar tarjeta';
                            submitButton.textContent = 'Actualizar tarjeta';
                            cancelEditButton.style.display = 'inline-block';
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

            document.querySelectorAll('#flashAlert').forEach((alert) => {
                setTimeout(() => {
                    alert.classList.remove('show');
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.5s ease';
                    setTimeout(() => alert.remove(), 500);
                }, 4000);
            });

            resetForm();
        });
    </script>
@endsection
