 <nav class="main-header navbar navbar-expand navbar-white navbar-light">
     <!-- Left navbar links -->
     <ul class="navbar-nav">
         <li class="nav-item">
             <a class="nav-link " data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
         </li>
         
     </ul>
    
  
     <!-- SEARCH FORM -->
     
     

     <!-- Right navbar links -->
     <ul class="navbar-nav ml-auto">
         <!-- Messages Dropdown Menu -->
         <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
              <i class="far fa-envelope fa-2x"></i>
              <span id="po-notif-count" class="badge badge-danger navbar-badge">0</span>
            </a>
            <div id="po-notif-menu" class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
              <audio id="po-sound" src="{{ asset('mp3/notif.mp3') }}" preload="auto"></audio>

              <!-- Notifikasi akan diisi JS -->
              <span class="dropdown-item text-center">Loading...</span>
            </div>
          </li>
          
         <!-- Notifications Dropdown Menu -->
         
         
     </ul>
 </nav>

 
