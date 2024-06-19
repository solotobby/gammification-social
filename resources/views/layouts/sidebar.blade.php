<div class="content-side">

  

    @if(auth()->user()->hasRole('admin'))
    <ul class="nav-main">
      <li class="nav-main-item">
        <a class="nav-main-link" href="{{ url('home') }}">
          <i class="nav-main-link-icon fa fa-home"></i>
          <span class="nav-main-link-name">Dashboard</span>
        </a>
        <a class="nav-main-link" href="{{ url('user/list') }}">
          <i class="nav-main-link-icon fa fa-list"></i>
          <span class="nav-main-link-name">User List</span>
        </a>

        <a class="nav-main-link" href="{{ url('send/access/code') }}">
          <i class="nav-main-link-icon fa fa-list"></i>
          <span class="nav-main-link-name">Send Access Codes</span>
        </a>

        <a class="nav-main-link" href="{{ url('validate/code') }}">
          <i class="nav-main-link-icon fa fa-list"></i>
          <span class="nav-main-link-name">Validate Access Codes</span>
        </a>

        <a class="nav-main-link" href="{{ url('partner/list') }}">
          <i class="nav-main-link-icon fa fa-list"></i>
          <span class="nav-main-link-name">Partner</span>
        </a>

        <a class="nav-main-link" href="{{ url('partner/transaction') }}">
          <i class="nav-main-link-icon fa fa-list"></i>
          <span class="nav-main-link-name">Partner Transaction</span>
        </a>
       
      </li>
    </ul>
      @else
      <ul class="nav-main">
        <li class="nav-main-item">
          <a class="nav-main-link" href="{{ url('home') }}">
            <i class="nav-main-link-icon fa fa-home"></i>
            <span class="nav-main-link-name">Dashboard</span>
         
          </a>
          @if(Auth::user())
          <a class="nav-main-link" href="{{ url('profile/'.auth()->user()->id) }}">
              <i class="nav-main-link-icon fa fa-user"></i>
              <span class="nav-main-link-name">Profile</span>
          </a>
          @else
  
            <a class="nav-main-link" href="{{ url('register') }}">
              <i class="nav-main-link-icon fa fa-user"></i>
              <span class="nav-main-link-name">Profile</span>
            </a>
          @endif
          <a class="nav-main-link" href="{{ url('analytics') }}">
            <i class="nav-main-link-icon si si-bar-chart"></i>
            <span class="nav-main-link-name">Analytics</span>
        </a>
          <a class="nav-main-link" href="{{ url('settings') }}">
              <i class="nav-main-link-icon si si-settings"></i>
              <span class="nav-main-link-name">Settings</span>
          </a>
          <a class="nav-main-link" href="{{ url('wallets') }}">
            <i class="nav-main-link-icon fa fa-wallet"></i>
            <span class="nav-main-link-name">Wallets</span>
          </a>
          <a class="nav-main-link" href="{{ url('partner') }}">
            <i class="nav-main-link-icon fa fa-briefcase"></i>
            <span class="nav-main-link-name">Become a Partner</span>
          </a>
  
          <a class="nav-main-link" href="{{ url('how/to/earn') }}">
            <i class="nav-main-link-icon fa fa-usd"></i>
            <span class="nav-main-link-name">How to Earn</span>
          </a>
  
        </li>
        
        {{-- <li class="nav-main-heading">Pages</li>
        <li class="nav-main-item">
          <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="false" href="#">
            <i class="nav-main-link-icon fa fa-rocket"></i>
            <span class="nav-main-link-name">Dashboards</span>
          </a>
          <ul class="nav-main-submenu">
            <li class="nav-main-item">
              <a class="nav-main-link" href="be_pages_dashboard_all.html">
                <span class="nav-main-link-name">All</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="be_pages_dashboard_v1.html">
                <span class="nav-main-link-name">Corporate v1</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="db_social.html">
                <span class="nav-main-link-name">Social</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="db_messages.html">
                <span class="nav-main-link-name">Messages</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="db_dark.html">
                <span class="nav-main-link-name">Dark</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="db_minimal.html">
                <span class="nav-main-link-name">Minimal</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="db_modern.html">
                <span class="nav-main-link-name">Modern</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="db_classic_boxed.html">
                <span class="nav-main-link-name">Classic Boxed</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="db_banking.html">
                <span class="nav-main-link-name">Banking</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="db_crypto.html">
                <span class="nav-main-link-name">Crypto</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="db_hosting.html">
                <span class="nav-main-link-name">Hosting</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="db_booking.html">
                <span class="nav-main-link-name">Booking</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="db_gaming.html">
                <span class="nav-main-link-name">Gaming</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="db_tasks.html">
                <span class="nav-main-link-name">Tasks</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="db_medical.html">
                <span class="nav-main-link-name">Medical</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="db_travel.html">
                <span class="nav-main-link-name">Travel</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="db_social_compact.html">
                <span class="nav-main-link-name">Social Compact</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="db_chat.html">
                <span class="nav-main-link-name">Chat</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="db_analytics.html">
                <span class="nav-main-link-name">Analytics</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="db_corporate_slim.html">
                <span class="nav-main-link-name">Corporate Slim</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="db_wp_post.html">
                <span class="nav-main-link-name">WP Post</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="db_file_hosting.html">
                <span class="nav-main-link-name">File Hosting</span>
              </a>
            </li>
          </ul>
        </li>
        <li class="nav-main-item">
          <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="false" href="#">
            <i class="nav-main-link-icon fa fa-user-friends"></i>
            <span class="nav-main-link-name">Auth</span>
          </a>
          <ul class="nav-main-submenu">
            <li class="nav-main-item">
              <a class="nav-main-link" href="be_pages_auth_all.html">
                <span class="nav-main-link-name">All</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="op_auth_signin.html">
                <span class="nav-main-link-name">Sign In</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="op_auth_signin_box.html">
                <span class="nav-main-link-name">Sign In Box</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="op_auth_signin_box_alt.html">
                <span class="nav-main-link-name">Sign In Box Alt</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="op_auth_signup.html">
                <span class="nav-main-link-name">Sign Up</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="op_auth_signup_box.html">
                <span class="nav-main-link-name">Sign Up Box</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="op_auth_signup_box_alt.html">
                <span class="nav-main-link-name">Sign Up Box Alt</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="op_auth_lock.html">
                <span class="nav-main-link-name">Lock Screen</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="op_auth_lock_box.html">
                <span class="nav-main-link-name">Lock Screen Box</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="op_auth_lock_box_alt.html">
                <span class="nav-main-link-name">Lock Screen Box Alt</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="op_auth_reminder.html">
                <span class="nav-main-link-name">Pass Reminder</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="op_auth_reminder_box.html">
                <span class="nav-main-link-name">Pass Reminder Box</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="op_auth_reminder_box_alt.html">
                <span class="nav-main-link-name">Pass Reminder Box Alt</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="op_auth_two_factor.html">
                <span class="nav-main-link-name">Two Factor</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="op_auth_two_factor_box.html">
                <span class="nav-main-link-name">Two Factor Box</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="op_auth_two_factor_box_alt.html">
                <span class="nav-main-link-name">Two Factor Box Alt</span>
              </a>
            </li>
          </ul>
        </li>
        <li class="nav-main-item">
          <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="false" href="#">
            <i class="nav-main-link-icon fa fa-ghost"></i>
            <span class="nav-main-link-name">Error</span>
          </a>
          <ul class="nav-main-submenu">
            <li class="nav-main-item">
              <a class="nav-main-link" href="be_pages_error_all.html">
                <span class="nav-main-link-name">All</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="op_error_400.html">
                <span class="nav-main-link-name">400</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="op_error_401.html">
                <span class="nav-main-link-name">401</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="op_error_403.html">
                <span class="nav-main-link-name">403</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="op_error_404.html">
                <span class="nav-main-link-name">404</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="op_error_500.html">
                <span class="nav-main-link-name">500</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="op_error_503.html">
                <span class="nav-main-link-name">503</span>
              </a>
            </li>
          </ul>
        </li>
        <li class="nav-main-item">
          <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="false" href="#">
            <i class="nav-main-link-icon fa fa-coffee"></i>
            <span class="nav-main-link-name">Get Started</span>
          </a>
          <ul class="nav-main-submenu">
            <li class="nav-main-item">
              <a class="nav-main-link" href="gs_backend.html">
                <span class="nav-main-link-name">Backend</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="gs_backend_boxed.html">
                <span class="nav-main-link-name">Backend Boxed</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="gs_landing.html">
                <span class="nav-main-link-name">Landing</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="gs_rtl_backend.html">
                <span class="nav-main-link-name">RTL Backend</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="gs_rtl_backend_boxed.html">
                <span class="nav-main-link-name">RTL Backend Boxed</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link" href="gs_rtl_landing.html">
                <span class="nav-main-link-name">RTL Landing</span>
              </a>
            </li>
          </ul>
        </li> --}}
      </ul>
    @endif
  </div>