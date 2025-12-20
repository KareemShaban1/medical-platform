<div class="navbar-custom">
	<ul class="list-unstyled topbar-menu float-end mb-0">
		<li class="dropdown notification-list topbar-dropdown">
			<a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#"
				role="button" aria-haspopup="false" aria-expanded="false">
				@if (App::getLocale() == 'ar')
				{{ LaravelLocalization::getCurrentLocaleName() }}
				<img src="{{ asset('backend/assets/images/flags/eg.png') }}" alt="">
				@else
				{{ LaravelLocalization::getCurrentLocaleName() }}
				<img src="{{ asset('backend/assets/images/flags/us.png') }}" alt="">
				@endif
			</a>

			<div class="dropdown-menu dropdown-menu-end dropdown-menu-animated topbar-dropdown-menu">
				@foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
				<a class="dropdown-item notify-item" rel="alternate"
					hreflang="{{ $localeCode }}"
					href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
					{{ $properties['native'] }}
				</a>
				@endforeach
			</div>
		</li>

		<li class="dropdown notification-list">
			<a class="nav-link dropdown-toggle nav-user arrow-none me-0" data-bs-toggle="dropdown"
				href="#" role="button" aria-haspopup="false" aria-expanded="false">
				<span class="account-user-avatar">
					<img src="{{asset('backend/assets/images/users/user.png')}}"
						alt="user-image" class="rounded-circle">
				</span>
				<span>
					<span class="account-user-name">
						{{ auth('affiliate')->user()->name ?? 'Affiliate' }}
					</span>
					<span class="account-position">{{ __('Affiliate') }}</span>
				</span>
			</a>
			<div class="dropdown-menu dropdown-menu-end dropdown-menu-animated topbar-dropdown-menu profile-dropdown">
				<div class=" dropdown-header noti-title">
					<h6 class="text-overflow m-0">{{ __('Welcome!') }}</h6>
				</div>

				<a href="{{ route('home') }}" class="dropdown-item notify-item">
					<i class="uil-home-alt me-1"></i>
					<span>{{ __('Back to Home') }}</span>
				</a>

				<div class="dropdown-divider"></div>

				<form method="POST" action="{{ route('affiliate.logout') }}">
					@csrf
					<a class="dropdown-item" href="#"
						onclick="event.preventDefault(); this.closest('form').submit();">
						<i class="mdi mdi-lock-outline me-1"></i>
						{{ __('Logout') }}
					</a>
				</form>
			</div>
		</li>
	</ul>
	<button class="button-menu-mobile open-left">
		<i class="mdi mdi-menu"></i>
	</button>
</div>
