<!DOCTYPE html>
<html lang="en">

<head> 

    <!-- Basic Page Needs
    ================================================== -->
    <title>XpertOpinion</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Courseplus is - Professional A unique and beautiful collection of UI elements">

    <!-- Favicon -->
    <link href="/assets/images/favicon.png" rel="icon" type="image/png">

    <!-- icons
    ================================================== -->
    <link rel="stylesheet" href="/assets/css/icons.css">

    <!-- CSS
    ================================================== -->
    <link rel="stylesheet" href="/assets/css/uikit.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    
    <style>
	    .categories  {
			display: block;
			height: 50px;    
	    }
	    
	    .categories li {
		    float: left;
		    margin-right: 10px;
		    color: #ff3665;
		    font-weight: bold;
		    font-size: 12px;
	    }
	    
	    .grid .item {
		    border: 0px solid #fff;
		    cursor: pointer;
		    margin-bottom: 10px;
	    }
	    
	    .grid .item.active {
		    border: 0px solid #fff;
		    background: #ff36654a;
	    }
    </style>

</head>

<body>


    <div id="wrapper" class="horizontal">
        
        <!--  Header  -->
        <header  uk-sticky>
            <div class="header_inner">
                <div class="left-side">
    
                    <!-- Logo -->
                    <div id="logo">
                        <a href="/experts">
                            <img src="https://xpertopinion.be/wp-content/uploads/2022/12/logo-with-in-raster.png" alt="" style="width: 150px; height: auto;">
                        </a>
                    </div>
    
                    <!-- icon menu for mobile -->
                    <div class="triger" uk-toggle="target: .header_menu ; cls: is-visible">
                    </div>
    
                    <!-- menu bar for mobile -->
                    <nav class="header_menu">
                        <ul> 
<!--
                            <li> 
                                <a href="#"> Courses</a> 
                                <div uk-drop="mode: click" class="category-dropdown">
                                    <ul>
                                        <li> <a href="courses.html">  <ion-icon name="newspaper-outline" class="is-icon"></ion-icon> Web Development </a></li>
                                        <li> <a href="courses.html">  <ion-icon name="leaf-outline" class="is-icon"></ion-icon> Mobile App </a> </li>
                                        <li> <a href="courses.html">  <ion-icon name="briefcase-outline" class="is-icon"></ion-icon> Business </a> </li>
                                        <li> <a href="courses.html">  <ion-icon name="color-palette-outline" class="is-icon"></ion-icon> Desings </a></li>
                                        <li> <a href="courses.html">  <ion-icon name="megaphone-outline" class="is-icon"></ion-icon> Marketing </a></li>
                                        <li> <a href="courses.html">  <ion-icon name="camera-outline" class="is-icon"></ion-icon> Photography </a> </li>
                                        <li> <a href="courses.html">  <ion-icon name="accessibility-outline" class="is-icon"></ion-icon> Life Style </a> </li>
                                    </ul>
                                </div>
                          
                            </li>
                           <li> <a href="categories.html" class="active"> Categories </a></li>
                            <li> <a href="episodes.html"> Episode  </a></li>
                            <li> <a href="books.html"> Book  </a></li>
                            <li> <a href="blog.html"> Blog</a></li>
                            <li> <a href="#">Pages</a>
                                <div uk-drop="mode: click" class="menu-dropdown">
                                    <ul>
                                        <li> <a href="pages-pricing.html"> Pricing</a></li>
                                        <li> <a href="pages-faq.html"> Faq </a></li>
                                       <li> <a href="pages-help.html"> Help </a></li>
                                        <li> <a href="pages-terms.html"> Terms </a></li>
                                        <li> <a href="pages-setting.html"> Setting </a></li>
                                        <li> <a href="#"> Development </a>
                                            <div class="menu-dropdown" uk-drop="mode: click;pos:right-top;animation: uk-animation-slide-right-small">
                                                <ul> 
                                                    <li><a href="development-elements.html"> Elements  </a></li>
                                                    <li><a href="development-components.html"> Compounents </a></li>
                                                    <li><a href="development-plugins.html"> Plugins </a></li>
                                                    <li><a href="development-icons.html"> Icons </a></li>
                                                </ul>
                                            </div>  
                                        </li>
                                        <li> <a href="pages-cart.html"> Shopping cart </a></li>
                                        <li> <a href="pages-payment-info.html"> Payment methods </a></li>
                                        <li> <a href="pages-account-info.html"> Account info </a></li>
                                    </ul>
                                </div>
                            </li>
-->
                        </ul>
                    </nav>
    
                    <!-- overly for small devices -->
                    <div class="overly" uk-toggle="target: .header_menu ; cls: is-visible"></div>
    
                </div>
                <div class="right-side">
    
                    <!-- messages -->
                    <a href="#" class="header_widgets">
                        <ion-icon name="notifications-outline" class="is-icon"></ion-icon>
                        <span> 2 </span>
                    </a>
                    <div uk-drop="mode: click" class="header_dropdown">
                        <div class="drop_headline">
                            <h4>Messages </h4>
                            <div class="btn_action">
                                <a href="#">
                                    <ion-icon name="settings-outline" uk-tooltip="title: Message settings ; pos: left"></ion-icon>
                                </a>
                                <a href="#">
                                    <ion-icon name="checkbox-outline" uk-tooltip="title: Mark as read all ; pos: left"></ion-icon>
                                </a>
                            </div>
                        </div>
                        <ul class="dropdown_scrollbar" data-simplebar>
                            <li>
                                <a href="#">
                                    <div class="drop_avatar"> <img src="/assets/images/avatars/avatar-1.jpg" alt="">
                                    </div>
                                    <div class="drop_content">
                                        <strong> John menathon </strong> <span class="time"> 6:43 PM</span>
                                        <p> Lorem ipsum dolor sit amet, consectetur </p>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <div class="drop_avatar"> <img src="/assets/images/avatars/avatar-2.jpg" alt="">
                                    </div>
                                    <div class="drop_content">
                                        <strong> Zara Ali </strong> <span class="time">12:43 PM</span>
                                        <p> Lorem ipsum dolor sit amet, consectetur </p>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <div class="drop_avatar"> <img src="/assets/images/avatars/avatar-3.jpg" alt="">
                                    </div>
                                    <div class="drop_content">
                                        <strong> Mohamed Ali </strong> <span class="time"> Wed</span>
                                        <p> Lorem ipsum dolor sit amet, consectetur </p>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <div class="drop_avatar"> <img src="/assets/images/avatars/avatar-1.jpg" alt="">
                                    </div>
                                    <div class="drop_content">
                                        <strong> John menathon </strong> <span class="time"> Sun </span>
                                        <p> Lorem ipsum dolor sit amet, consectetur </p>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <div class="drop_avatar"> <img src="/assets/images/avatars/avatar-2.jpg" alt="">
                                    </div>
                                    <div class="drop_content">
                                        <strong> Zara Ali </strong> <span class="time"> Fri </span>
                                        <p> Lorem ipsum dolor sit amet, consectetur </p>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <div class="drop_avatar"> <img src="/assets/images/avatars/avatar-3.jpg" alt="">
                                    </div>
                                    <div class="drop_content">
                                        <strong> Mohamed Ali </strong> <span class="time">1 Week ago</span>
                                        <p> Lorem ipsum dolor sit amet, consectetur </p>
                                    </div>
                                </a>
                            </li>
                        </ul>
                        <a href="#" class="see-all">See all</a>
                    </div>
    
                     <!-- profile -->
                    <a href="#">
                        <img src="https://placebeard.it/400" class="header_widgets_avatar" alt="">
                    </a>
                    <div uk-drop="mode: click;offset:5" class="header_dropdown profile_dropdown">
                        <ul>   
                            <li>
                                <a href="#" class="user">
                                    <div class="user_avatar">
                                        <img src="https://placebeard.it/400" alt="">
                                    </div>
                                    <div class="user_name">
                                        <div> Jan Luts </div>
                                    </div>
                                </a>
                            </li>
                            <li> 
                                <hr>
                            </li>
                            <li> 
                                <a href="#">
                                    <ion-icon name="person-circle-outline" class="is-icon"></ion-icon>
                                     Profiel
                                </a>
                            </li>
                            <li> 
                                <a href="#">
                                    <ion-icon name="card-outline" class="is-icon"></ion-icon>
                                    Facturen
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <ion-icon name="settings-outline" class="is-icon"></ion-icon>
                                    Instellingen  
                                </a>
                            </li>
                            <li> 
                                <hr>
                            </li>
                            <li> 
                                <a href="#">
                                    <ion-icon name="log-out-outline" class="is-icon"></ion-icon>
                                    Log Out 
                                </a>
                            </li>
                        </ul>
                    </div>
    
                </div>
            </div>
        </header>
  
        <div class="max-w-5xl md:p-5 mx-auto">
 
            <div class="lg:flex lg:space-x-10 bg-white rounded-md shadow max-w-3x  mx-auto md:p-8 p-3 mt-10" id="book-me">
                <div class="w-full flex-shrink-0 mt-10 lg:m-0"> 
                    
                    <div> 
	                    <center>
							<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script><lottie-player src="https://lottie.host/88dfc2c6-0330-44e4-b089-57fb49b9ec5c/LgooJiCDra.json" background="##FFFFFF" speed="1" style="width: 300px; height: 300px" autoplay direction="1" mode="normal"></lottie-player>
	                        <h2 class="font-semibold mb-3 text-xl lg:text-3xl">Expert succesvol geboekt!</h2>
	                    </center>
                    </div>                                   
                </div>
                
            </div>


        </div>
 
         <!-- footer -->
         <div class="lg:mt-28 mt-10 mb-7 px-12 border-t pt-7">
            <div class="flex flex-col items-center justify-between lg:flex-row max-w-6xl mx-auto lg:space-y-0 space-y-3">
                <p class="capitalize font-medium"> © copyright <?=date('Y');?>  XpertOpinion</p>
                <div class="lg:flex space-x-4 text-gray-700 capitalize hidden">
                    <a href="#"> About</a>
                    <a href="#"> Help</a>
                    <a href="#"> Terms</a>
                    <a href="#"> Privacy</a>
                </div>
            </div>
        </div>

    </div>
        
    <!-- Javascript
    ================================================== -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
   <script src="/assets/js/uikit.js"></script>
    <script src="/assets/js/tippy.all.min.js"></script>
    <script src="/assets/js/simplebar.js"></script>
    <script src="/assets/js/custom.js"></script>
    <script src="/assets/js/bootstrap-select.min.js"></script>
    <script src="https://unpkg.com/ionicons@5.2.3/dist/ionicons.js"></script>
    
	<script>
	    $(document).ready(function () {
		    $('.grid .item').on('click', function (e) {
	            e.preventDefault();
	
	            // Remove active class from all links
	            $(this).parent().find('.item').removeClass('active');
	
	            // Add active class to the clicked link
	            $(this).addClass('active');
	            
	            $('.btn-next').click();
	        });
	        
	        $('.calendar li').click(function(e) {
		        e.preventDefault();
		        
		         $(this).addClass('active');
		         
		         $('.btn-next').click();
	        });
	        
	        let currentStep = 1;
	
	        $('.btn-next').on('click', function () {
	            if (currentStep < 4) {
	                $(`.step[data-step=${currentStep}]`).addClass('hidden');
	                currentStep++;
	                $(`.step[data-step=${currentStep}]`).removeClass('hidden');
	            }
	            
		        if(currentStep == 4) {
			        $('.buttons').hide();
		        }
	        });
	
	        $('.btn-prev').on('click', function () {
	            if (currentStep > 1) {
	                $(`.step[data-step=${currentStep}]`).addClass('hidden');
	                currentStep--;
	                $(`.step[data-step=${currentStep}]`).removeClass('hidden');
	            }
	        });
	    });
	</script>

</body>

</html>
