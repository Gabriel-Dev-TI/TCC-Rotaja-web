<nav class="navbar navbar-expand navbar-light navbar-bg">
				<a class="sidebar-toggle js-sidebar-toggle">
      		    <i class="hamburger align-self-center"></i>
        		</a>

				<div class="navbar-collapse collapse">
						
                	 <span class="text-dark navbar-nav navbar-align">{{ \Illuminate\Support\Str::before(trim(auth()->user()->nome), ' ') }}</span>
				</div>
            	 	 
							
			</nav>