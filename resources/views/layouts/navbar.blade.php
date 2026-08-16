<nav class="navbar navbar-expand navbar-light navbar-bg">
				<a class="sidebar-toggle js-sidebar-toggle">
      		    <i class="hamburger align-self-center"></i>
        		</a>

				<div class="navbar-collapse collapse">
						
                	 <span class="text-dark navbar-nav navbar-align">{{ \Illuminate\Support\Str::before(trim(auth()->user()->nome), ' ') }}</span>
					  <div
							class="rounded-circle d-flex align-items-center justify-content-center mx-2"
							style="
								width: 36px;
								height: 36px;
								background: linear-gradient(135deg, #6d4aff, #8b5cf6);
								color: white;
								font-size: 14px;
								font-weight: 600;
							">
            {{ strtoupper(substr(auth()->user()->nome ?? 'U', 0, 1)) }}
        </div>
				</div>
            	 	 
							
			</nav>