<div class="leftside-menu">
	<a class="logo text-center" href="#">
		<span class="logo-lg">
			<i class="fas fa-laptop-code"></i> <span class="logo-text">{{ config('app.name') }}</span>
		</span>
		<span class="logo-sm">
			<i class="fas fa-laptop-code"></i>
		</span>
	</a>

	<a class="logo text-center logo-dark">
		<span class="logo-lg text-white">
			{{ config('app.name') }}
		</span>
		<span class="logo-sm text-white">
			{{ config('app.name') }}
		</span>
	</a>

	<div class="h-100" id="leftside-menu-container" data-simplebar="">
		<div class="px-3 py-2">
			<input type="text" id="sidebar-search" class="form-control" placeholder="{{ __('Search menu...') }}"
				autocomplete="off">
		</div>

		<ul class="side-nav">
			<li class="side-nav-item">
				<a href="{{ route('affiliate.dashboard') }}" class="side-nav-link">
					<i class="uil-home-alt"></i>
					<span> {{ __('Affiliate Dashboard') }} </span>
				</a>
			</li>
			<li class="side-nav-item">
				<a href="{{ route('affiliate.tickets.index') }}" class="side-nav-link">
					<i class="uil-headphones"></i>
					<span> {{ __('Support Tickets') }} </span>
				</a>
			</li>
		</ul>

		<div class="clearfix"></div>
	</div>
</div>