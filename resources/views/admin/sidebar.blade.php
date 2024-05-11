<section class="deshboard_main" id="hassidebar">
	<div class="sidebar">
	<ul class="navbar-nav-head d-xl-none d-block">
		<li class="nav-item">
			<form action="">
					<div class="form-group search">
					<input id="my-input" class="form-control formsearch" type="text" name="search" placeholder="SEARCH">
					<button class="button"><img src="{{ asset('assets/images/search_icon.svg') }}" alt=""></button>
					</div>
			</form>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="#"><span>NFTCARD</span></a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="#"><span>RANK</span></a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="#"><span>COIN</span></a>
		</li>
	</ul>
		<ul class="navbar-nav">
		<li class="nav-item {{ (Request::route()->getName() == 'admin.admin_dashboard') ? 'active' : '' }}">
			<a class="nav-link" href="{{ route('admin.admin_dashboard') }}"><img
			src="{{ asset('assets/images/sidebar/home_icon.svg') }}" alt="" /><span>Dashboard</span></a>
		</li>
		<li class="nav-item {{ (Request::route()->getName() == 'admin.user.index' || Request::route()->getName() == 'admin.user.create' || Request::route()->getName() == 'admin.user.edit') ? 'active' : '' }}">
			<a class="nav-link" href="{{route('admin.user.index')}}"><img
				src="{{ asset('assets/images/sidebar/guest_icon.svg') }}" alt="" /><span>Use info</span></a>
		</li>
		<li class="nav-item {{ (Request::route()->getName() == 'admin.my_profile') ? 'active' : '' }}">
			<a class="nav-link" href="{{route('admin.my_profile')}}"><img
				src="{{ asset('assets/images/sidebar/guest_icon.svg') }}" alt="" /><span>My Profile</span></a>
		</li>
		<li class="nav-item {{ (Request::route()->getName() == 'admin.news.index') ? 'active' : '' }}">
			<a class="nav-link" href="{{route('admin.news.index')}}"><img
				src="{{ asset('assets/images/sidebar/bell_icon.svg') }}" alt="" /><span>News Notification</span></a>
		</li>
		<li class="nav-item {{ (Request::route()->getName() == 'admin.get_money') ? 'active' : '' }}">
			<a class="nav-link" href="{{route('admin.get_money')}}"><img
				src="{{ asset('assets/images/sidebar/bell_icon.svg') }}" alt="" /><span>Transactions</span></a>
		</li>
		<li class="nav-item {{ (Request::route()->getName() == 'admin.questions.index') ? 'active' : '' }}">
			<a class="nav-link" href="{{route('admin.questions.index')}}"><img
				src="{{ asset('assets/images/sidebar/bell_icon.svg') }}" alt="" /><span>Security Questions</span></a>
		</li>
		<li class="nav-item {{ (Request::route()->getName() == 'admin.setting.index') ? 'active' : '' }}">
			<a class="nav-link" href="{{route('admin.setting.index')}}"><img
				src="{{ asset('assets/images/sidebar/bell_icon.svg') }}" alt="" /><span>Setting</span></a>
		</li>
		<li class="nav-item">

			<a class="nav-link" href="{{route('admin.admin_logout')}}"><img src="{{ asset('assets/images/sidebar/exit_icon.svg') }}"
				alt="" /><span>Logout</span></a>

		</li>

		</ul>

	</div>
  <!-- </section> -->
