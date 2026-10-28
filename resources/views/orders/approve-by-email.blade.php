<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Confirmar Orden</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }
        .order-info {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        textarea {
            width: 100%;
            min-height: 100px;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        .help-text {
            color: #666;
            font-size: 0.9em;
            margin-top: 5px;
        }
        .submit-button {
            display: block;
            width: 100%;
            padding: 12px 24px;
            margin-top: 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }
        .submit-button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Confirmar Orden de Pago</h1>

        <div class="order-info">
            <p><strong>Orden #{{ $order->id }}</strong></p>
            <p><strong>Solicitante:</strong> {{ $order->user->name }}</p>
            <p><strong>Departamento:</strong> {{ $order->user->department }}</p>
            <p><strong>Fecha:</strong> {{ $order->created_at->format('d/m/Y') }}</p>
        </div>

        <form action="{{ route('orders.approve-by-email.submit', ['token' => $token]) }}" method="POST">
            @csrf
            <label for="comments">Observación</label>
            <textarea id="comments" name="comments" maxlength="1000" placeholder="Observación opcional">{{ old('comments') }}</textarea>
            <p class="help-text">Este campo no es obligatorio. Si lo completas, se incluirá en el correo de confirmación con tu nombre.</p>
            @error('comments')
                <p style="color: #dc3545;">{{ $message }}</p>
            @enderror
            <button type="submit" class="submit-button">Confirmar Orden</button>
        </form>
    </div>
</body>
</html>
