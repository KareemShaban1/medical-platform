<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Prescription - {{ $clinic->name ?? 'Clinic' }}</title>
	<style>
	* {
		margin: 0;
		padding: 0;
		box-sizing: border-box;
	}

	body {
		font-family: 'Arial', 'Helvetica', sans-serif;
		background: #f5f5f5;
		padding: 20px;
		direction: rtl;
	}

	.print-actions {
		position: fixed;
		top: 20px;
		left: 20px;
		z-index: 1000;
		display: flex;
		gap: 10px;
	}

	.print-actions button {
		padding: 10px 20px;
		background: #1a5f7a;
		color: white;
		border: none;
		border-radius: 5px;
		cursor: pointer;
		font-size: 14px;
		font-weight: bold;
		box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
		transition: background 0.3s;
	}

	.print-actions button:hover {
		background: #17a2b8;
	}

	@media print {
		.print-actions {
			display: none;
		}
	}

	.prescription-container {
		max-width: 8.5in;
		margin: 0 auto;
		background: white;
		box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
		position: relative;
		min-height: 11in;
	}

	/* Header Section */
	.prescription-header {
		background: linear-gradient(to right, #1a5f7a 0%, #1a5f7a 60%, #17a2b8 60%, #17a2b8 100%);
		position: relative;
		padding: 30px 40px;
		color: white;
	}

	.header-diagonal {
		position: absolute;
		top: 0;
		left: 60%;
		width: 0;
		height: 0;
		border-left: 40px solid #17a2b8;
		border-top: 100px solid transparent;
	}

	.header-content {
		display: flex;
		justify-content: space-between;
		align-items: center;
		position: relative;
		z-index: 1;
	}

	.clinic-info {
		text-align: right;
	}

	.clinic-name {
		font-size: 32px;
		font-weight: bold;
		margin-bottom: 8px;
	}

	.clinic-address {
		font-size: 14px;
		margin-bottom: 4px;
	}

	.clinic-phone {
		font-size: 14px;
	}

	/* Patient Information Section */
	.patient-info-section {
		padding: 30px 40px;
		background: white;
	}

	.patient-info-grid {
		display: grid;
		grid-template-columns: 1fr 1fr;
		gap: 20px;
		margin-bottom: 30px;
	}

	.info-field {
		margin-bottom: 15px;
	}

	.info-label {
		font-size: 12px;
		color: #666;
		margin-bottom: 5px;
		font-weight: 600;
	}

	.info-line {
		border-bottom: 2px solid #333;
		padding-bottom: 5px;
		min-height: 25px;
		font-size: 14px;
	}

	.info-line.empty {
		border-bottom: 2px solid #ccc;
	}

	/* Rx Symbol */
	.rx-symbol {
		font-size: 120px;
		font-weight: bold;
		color: #1a5f7a;
		text-align: center;
		margin: 40px 0;
		line-height: 1;
	}

	/* Prescription Items Section */
	.prescription-items {
		padding: 0 40px 30px;
	}

	.prescription-items-title {
		font-size: 18px;
		font-weight: bold;
		color: #333;
		margin-bottom: 20px;
		border-bottom: 2px solid #1a5f7a;
		padding-bottom: 10px;
	}

	.prescription-item {
		margin-bottom: 25px;
		padding: 15px;
		background: #f9f9f9;
		border-right: 4px solid #17a2b8;
	}

	.item-drug-name {
		font-size: 16px;
		font-weight: bold;
		color: #1a5f7a;
		margin-bottom: 8px;
	}

	.item-details {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
		gap: 10px;
		font-size: 13px;
		color: #555;
	}

	.item-detail {
		display: flex;
		flex-direction: column;
	}

	.item-detail-label {
		font-size: 17px;
		color: #888;
		margin-bottom: 3px;
	}

	.item-detail-value {
		font-weight: 600;
		color: #333;
	}

	.item-notes {
		margin-top: 8px;
		font-size: 12px;
		color: #666;
		font-style: italic;
	}

	/* Notes Section */
	.prescription-notes {
		padding: 0 40px 30px;
		margin-top: 20px;
	}

	.notes-title {
		font-size: 16px;
		font-weight: bold;
		color: #333;
		margin-bottom: 10px;
	}

	.notes-content {
		font-size: 14px;
		color: #555;
		line-height: 1.6;
		padding: 15px;
		background: #f9f9f9;
		border-right: 4px solid #17a2b8;
	}

	/* Doctor Signature Section */
	.doctor-signature {
		padding: 40px 40px 30px;
		text-align: right;
		margin-top: auto;
	}

	.signature-line {
		border-top: 2px solid #333;
		width: 300px;
		margin-left: auto;
		margin-bottom: 10px;
		padding-top: 5px;
	}

	.doctor-name {
		font-size: 16px;
		font-weight: bold;
		color: #1a5f7a;
		text-align: center;
	}

	/* Footer Logo */
	.footer-logo {
		position: absolute;
		bottom: 20px;
		right: 40px;
		width: 50px;
		height: 50px;
		background: #e0e0e0;
		border-radius: 4px;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 28px;
		font-weight: bold;
		color: #666;
	}

	/* Print Styles */
	@media print {
		body {
			background: white;
			padding: 0;
		}

		.prescription-container {
			box-shadow: none;
			max-width: 100%;
		}

		.no-print {
			display: none;
		}
	}

	/* Background Gradient */
	.prescription-container::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: linear-gradient(to bottom, rgba(255, 255, 255, 0) 0%, rgba(240, 240, 240, 0.3) 50%, rgba(255, 255, 255, 0) 100%);
		pointer-events: none;
		z-index: 0;
	}

	.prescription-container>* {
		position: relative;
		z-index: 1;
	}
	</style>
</head>

<body>
	<!-- Print/Download Actions -->
	<div class="print-actions no-print">
		<a href="{{ route('clinic.prescriptions.download', $appointment->id) }}" class="btn-download"
			style="text-decoration: none; display: inline-block;">
			<button type="button">📥 {{ __('Download PDF') }}</button>
		</a>
		<button type="button" onclick="window.print()">🖨️ {{ __('Print') }}</button>
	</div>

	<div class="prescription-container">
		<!-- Header Section -->
		<div class="prescription-header">
			<div class="header-content">
				<div></div>
				<div class="clinic-info">
					<div class="clinic-name">{{ $clinic->name ?? 'Arcosoft' }}</div>
					<div class="clinic-address">
						{{ $clinic->address ?? 'Orlando, FL 32801' }}</div>
					<div class="clinic-phone">{{ $clinic->phone ?? '222 555 777' }}</div>
				</div>
			</div>
		</div>

		<!-- Patient Information Section -->
		<div class="patient-info-section">
			<div class="patient-info-grid">
				<div>
					<div class="info-field">
						<div class="info-label">{{ __('Patient Name') }}:</div>
						<div class="info-line">{{ $patientUser->name ?? 'N/A' }}
						</div>
					</div>

				</div>
				<div>

					<div class="info-field">
						<div class="info-label">{{ __('Phone') }}:</div>
						<div class="info-line">{{ $patient->phone ?? 'N/A' }}</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Rx Symbol -->
		<div class="rx-symbol">Rx</div>

		<!-- Prescription Items -->
		@if($prescription && $prescription->items->count() > 0)
		<div class="prescription-items">
			<div class="prescription-items-title">{{ __('Drugs') }}</div>
			@foreach($prescription->items as $item)
			<div class="prescription-item">
				<div class="item-details">
					<div class="item-detail">
						<div class="item-detail-label">{{ __('Drug Name') }}:</div>
						<div class="item-detail-value">{{ $item->drug_name }}</div>
					</div>
					@if($item->dose)
					<div class="item-detail">
						<div class="item-detail-label">{{ __('Dose') }}:</div>
						<div class="item-detail-value">{{ $item->dose }}</div>
					</div>
					@endif
					@if($item->frequency)
					<div class="item-detail">
						<div class="item-detail-label">{{ __('Frequency') }}:</div>
						<div class="item-detail-value">{{ $item->frequency }}</div>
					</div>
					@endif
					@if($item->duration)
					<div class="item-detail">
						<div class="item-detail-label">{{ __('Duration') }}:</div>
						<div class="item-detail-value">{{ $item->duration }}</div>
					</div>
					@endif
					@if($item->notes)
					<div class="item-detail">
						<div class="item-detail-label">{{ __('Notes') }}:</div>
						<div class="item-notes">{{ $item->notes }}</div>
					</div>
					@endif
				</div>

			</div>
			@endforeach
		</div>
		@else
		<div class="prescription-items">
			<div class="prescription-items-title">{{ __('Drugs') }}</div>
			<div class="prescription-item">
				<div class="item-drug-name">{{ __('No prescription items available') }}</div>
			</div>
		</div>
		@endif

		<!-- Prescription Notes -->
		@if($prescription && $prescription->notes)
		<div class="prescription-notes">
			<div class="notes-title">{{ __('Additional Notes') }}:</div>
			<div class="notes-content">{{ $prescription->notes }}</div>
		</div>
		@endif

		<!-- Doctor Signature -->
		<div class="doctor-signature">
			<div class="signature-line"></div>
			<div class="doctor-name">Dr. {{ $doctor->name ?? 'N/A' }}</div>
		</div>

		<!-- Footer Logo -->
		<div class="footer-logo">T</div>
	</div>

	<script>
	// Handle print button
	document.addEventListener('DOMContentLoaded', function() {
		// Add print styles
		const style = document.createElement('style');
		style.textContent = `
			@media print {
				.print-actions { display: none !important; }
				body { padding: 0; background: white; }
				.prescription-container { box-shadow: none; margin: 0; }
			}
		`;
		document.head.appendChild(style);
	});

	// Auto print when page loads (optional)
	// window.onload = function() {
	//     window.print();
	// }
	</script>
</body>

</html>
