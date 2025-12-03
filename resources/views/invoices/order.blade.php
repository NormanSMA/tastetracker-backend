<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #{{ $order->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11pt;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
        }
        
        /* Header */
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #FF6B00;
            padding-bottom: 20px;
        }
        
        .header h1 {
            font-size: 28pt;
            color: #FF6B00;
            margin-bottom: 5px;
            font-weight: 700;
        }
        
        .header p {
            font-size: 10pt;
            color: #666;
        }
        
        /* Invoice Info */
        .invoice-info {
            margin-bottom: 30px;
            display: table;
            width: 100%;
        }
        
        .invoice-info-left,
        .invoice-info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .invoice-info h3 {
            font-size: 12pt;
            color: #FF6B00;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .invoice-info p {
            font-size: 10pt;
            margin-bottom: 5px;
        }
        
        .invoice-info strong {
            color: #000;
        }
        
        /* Order Details */
        .order-details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 25px;
        }
        
        .order-details table {
            width: 100%;
        }
        
        .order-details td {
            padding: 5px 10px;
            font-size: 10pt;
        }
        
        .order-details td:first-child {
            font-weight: 600;
            color: #555;
            width: 30%;
        }
        
        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        
        .items-table thead {
            background-color: #FF6B00;
            color: white;
        }
        
        .items-table th {
            padding: 12px;
            text-align: left;
            font-size: 10pt;
            font-weight: 600;
        }
        
        .items-table th.text-center {
            text-align: center;
        }
        
        .items-table th.text-right {
            text-align: right;
        }
        
        .items-table tbody tr {
            border-bottom: 1px solid #dee2e6;
        }
        
        .items-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .items-table td {
            padding: 10px 12px;
            font-size: 10pt;
        }
        
        .items-table .text-center {
            text-align: center;
        }
        
        .items-table .text-right {
            text-align: right;
        }
        
        /* Totals */
        .totals {
            margin-top: 20px;
            text-align: right;
        }
        
        .totals table {
            margin-left: auto;
            min-width: 300px;
        }
        
        .totals td {
            padding: 8px 15px;
            font-size: 11pt;
        }
        
        .totals .label {
            font-weight: 600;
            color: #555;
        }
        
        .totals .grand-total {
            background-color: #FF6B00;
            color: white;
            font-size: 14pt;
            font-weight: 700;
        }
        
        /* Footer */
        .footer {
            margin-top: 40px;
            text-align: center;
            padding-top: 20px;
            border-top: 2px solid #dee2e6;
        }
        
        .footer p {
            font-size: 10pt;
            color: #666;
            margin-bottom: 5px;
        }
        
        .footer .thank-you {
            font-size: 14pt;
            color: #FF6B00;
            font-weight: 600;
            margin-top: 15px;
        }
        
        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 3px;
            font-size: 9pt;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-pending { background-color: #ffc107; color: #000; }
        .status-preparing { background-color: #17a2b8; color: #fff; }
        .status-ready { background-color: #28a745; color: #fff; }
        .status-served { background-color: #6c757d; color: #fff; }
        .status-paid { background-color: #28a745; color: #fff; }
        .status-cancelled { background-color: #dc3545; color: #fff; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>TASTETRACKER</h1>
            <p>Restaurante & Bar | Tel: (555) 123-4567</p>
            <p>Dirección: Calle Principal 123, Ciudad</p>
        </div>
        
        <!-- Invoice Info -->
        <div class="invoice-info">
            <div class="invoice-info-left">
                <h3>FACTURA</h3>
                <p><strong>No.:</strong> #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
                <p><strong>Fecha:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Estado:</strong> 
                    <span class="status-badge status-{{ $order->status }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </p>
            </div>
            <div class="invoice-info-right">
                <h3>INFORMACIÓN DEL SERVICIO</h3>
                <p><strong>Mesa:</strong> {{ $order->table_identifier }} - {{ $order->area->name ?? 'N/A' }}</p>
                <p><strong>Tipo:</strong> {{ ucfirst($order->order_type) }}</p>
                @if($order->waiter)
                <p><strong>Mesero:</strong> {{ $order->waiter->name }}</p>
                @endif
                @if($order->customer)
                <p><strong>Cliente:</strong> {{ $order->customer->name }}</p>
                @elseif($order->guest_name)
                <p><strong>Cliente:</strong> {{ $order->guest_name }}</p>
                @endif
            </div>
        </div>
        
        <!-- Order Details (if notes exist) -->
        @if($order->notes)
        <div class="order-details">
            <table>
                <tr>
                    <td>Notas:</td>
                    <td>{{ $order->notes }}</td>
                </tr>
            </table>
        </div>
        @endif
        
        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%">Producto</th>
                    <th class="text-center" style="width: 15%">Cantidad</th>
                    <th class="text-right" style="width: 17.5%">Precio Unit.</th>
                    <th class="text-right" style="width: 17.5%">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->product->name }}</strong>
                        @if($item->notes)
                        <br><em style="font-size: 9pt; color: #666;">{{ $item->notes }}</em>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">C$ {{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">C$ {{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Totals -->
        <div class="totals">
            <table>
                <tr class="grand-total">
                    <td class="label">TOTAL:</td>
                    <td class="text-right">C$ {{ number_format($order->total, 2) }}</td>
                </tr>
            </table>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p class="thank-you">¡Gracias por su preferencia!</p>
            <p>Esta es una factura generada electrónicamente</p>
            <p>Para consultas: info@tastetracker.com</p>
        </div>
    </div>
</body>
</html>
