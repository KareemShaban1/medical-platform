@extends('frontend.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
	<div class="max-w-3xl mx-auto">
		<div class="bg-red-50 border-2 border-red-500 rounded-lg p-8 text-center mb-6">
			<svg class="w-20 h-20 mx-auto text-red-500 mb-4" fill="none" stroke="currentColor"
				viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
					d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
			</svg>
			<h1 class="text-3xl font-bold text-red-700 mb-2">{{__('order failed')}}
			</h1>
			<p class="text-gray-700 mb-4">
				{{__('sorry, your order could not be processed. please try again or contact support')}}
			</p>

			<a href="{{ route('checkout.index') }}" class="btn-primary">
				{{__('try again')}}
				<i class="fas fa-redo mx-2"></i>
			</a>
			<a href="{{ route('home') }}" class="btn-secondary">
				{{__('go to home')}}
				<i class="fas fa-home mx-2"></i>
			</a>
			<a href="{{ route('cart.index') }}" class="btn-primary">
				{{ __('view cart') }}
				<i class="fas fa-shopping-cart mx-2"></i>
			</a>
		</div>
	</div>
</div>
@endsection