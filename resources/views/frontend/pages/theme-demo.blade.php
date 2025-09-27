@extends('frontend.layouts.app')

@section('content')
<!-- Theme Demo Page -->
<div class="bg-gradient-primary">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
		<div class="text-center text-white">
			<h1 class="text-4xl font-bold mb-4">New Medical Platform Theme</h1>
			<p class="text-xl mb-8">Experience our beautiful gradient design with
				linear-gradient(rgba(0, 0, 0, 0.7), #079184)</p>
			<div class="flex flex-wrap justify-center gap-4">
				<button class="btn-primary">Primary Button</button>
				<button class="btn-secondary">Secondary Button</button>
			</div>
		</div>
	</div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
	<!-- Cards Section -->
	<div class="mb-16">
		<h2 class="text-3xl font-bold text-center mb-8">Theme Components</h2>
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
			<div class="card p-6">
				<h3 class="text-xl font-semibold mb-4 text-primary">Regular Card</h3>
				<p class="text-gray-600 mb-4">This is a regular card with the new theme styling.
				</p>
				<button class="btn-primary">Action</button>
			</div>

			<div class="card-gradient p-6">
				<h3 class="text-xl font-semibold mb-4">Gradient Card</h3>
				<p class="mb-4">This card uses the main gradient background.</p>
				<button class="btn-secondary">Action</button>
			</div>

			<div class="card p-6 border-gradient">
				<h3 class="text-xl font-semibold mb-4 text-primary">Border Gradient Card</h3>
				<p class="text-gray-600 mb-4">This card has a gradient border effect.</p>
				<button class="btn-primary">Action</button>
			</div>
		</div>
	</div>

	<!-- Badges Section -->
	<div class="mb-16">
		<h2 class="text-3xl font-bold text-center mb-8">Badges & Tags</h2>
		<div class="flex flex-wrap justify-center gap-4">
			<span class="badge badge-primary">Primary</span>
			<span class="badge badge-secondary">Secondary</span>
			<span class="badge badge-success">Success</span>
			<span class="badge badge-warning">Warning</span>
			<span class="badge badge-danger">Danger</span>
		</div>
	</div>

	<!-- Alerts Section -->
	<div class="mb-16">
		<h2 class="text-3xl font-bold text-center mb-8">Alert Messages</h2>
		<div class="max-w-2xl mx-auto space-y-4">
			<div class="alert alert-primary">
				<strong>Primary Alert:</strong> This is a primary alert message with the theme
				colors.
			</div>
			<div class="alert alert-success">
				<strong>Success Alert:</strong> This is a success alert message.
			</div>
			<div class="alert alert-warning">
				<strong>Warning Alert:</strong> This is a warning alert message.
			</div>
			<div class="alert alert-danger">
				<strong>Danger Alert:</strong> This is a danger alert message.
			</div>
		</div>
	</div>

	<!-- Form Section -->
	<div class="mb-16">
		<h2 class="text-3xl font-bold text-center mb-8">Form Elements</h2>
		<div class="max-w-2xl mx-auto">
			<div class="card p-6">
				<form class="space-y-6">
					<div>
						<label class="form-label">Full Name</label>
						<input type="text" class="form-input w-full"
							placeholder="Enter your full name">
					</div>
					<div>
						<label class="form-label">Email Address</label>
						<input type="email" class="form-input w-full"
							placeholder="Enter your email">
					</div>
					<div>
						<label class="form-label">Message</label>
						<textarea class="form-input w-full" rows="4"
							placeholder="Enter your message"></textarea>
					</div>
					<div class="flex gap-4">
						<button type="submit" class="btn-primary">Submit</button>
						<button type="button" class="btn-secondary">Cancel</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Gradient Text Section -->
	<div class="text-center">
		<h2 class="text-4xl font-bold text-gradient mb-4">Gradient Text Effect</h2>
		<p class="text-lg text-gray-600">This heading uses the gradient text effect from our theme.</p>
	</div>
</div>
@endsection
