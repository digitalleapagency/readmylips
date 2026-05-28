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
  
        <div class="container">

            <!-- Spacer -->
            <div class="page-spacer"></div>
               
            <div class="lg:flex lg:space-x-10">
            
                <div class="w-full md:space-y-12 space-y-5"> 
                    
                    <div>

                        <!-- title -->
                        <div class="mb-2">
                            <div class="text-xl font-semibold">Better decisions - Call an expert</div>
                            <div class="text-sm mt-2">In just one hour, XpertOpinion’s top-notch experts can offer valuable insights to elevate your projects and decisions. Covering both traditional and innovative domains within organizations, they provide a fresh perspective and helpful second opinions.</div>
                        </div>
        
                    </div>

                    <!--  Categories -->
                    <div>

                        <div class="sm:my-8 my-3 flex items-end justify-between">
                            <div>
                                <h2 class="text-xl font-semibold">Categorieën</h2>
                                <p class="font-medium text-gray-500 leading-6"> Vind hier de juiste experten. </p>
                            </div>
                        </div> 
        
                        <div class="relative -mt-3" uk-slider="finite: true">
                        
                            <div class="uk-slider-container px-1 py-3">
                                <ul class="uk-slider-items uk-child-width-1-5@m uk-child-width-1-3@s uk-child-width-1-2 uk-grid-small uk-grid">
                                    <li>
                                        <div class="rounded-md overflow-hidden relative w-full h-36">
                                            <div class="absolute w-full h-3/4 -bottom-12 bg-gradient-to-b from-transparent to-gray-800 z-10">
                                            </div>
                                            <img src="https://placebeard.it/400?random=1" class="absolute w-full h-full object-cover" alt="">
                                            <div class="absolute bottom-0 w-full p-3 text-white z-20 font-semibold text-lg"> Design </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="rounded-md overflow-hidden relative w-full h-36">
                                            <div class="absolute w-full h-3/4 -bottom-12 bg-gradient-to-b from-transparent to-gray-800 z-10">
                                            </div>
                                            <img src="https://placebeard.it/400?random=2" class="absolute w-full h-full object-cover"
                                                alt="">
                                            <div class="absolute bottom-0 w-full p-3 text-white z-20 font-semibold text-lg"> Marketing </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="rounded-md overflow-hidden relative w-full h-36">
                                            <div class="absolute w-full h-3/4 -bottom-12 bg-gradient-to-b from-transparent to-gray-800 z-10">
                                            </div>
                                            <img src="https://placebeard.it/400?random=3" class="absolute w-full h-full object-cover"
                                                alt="">
                                            <div class="absolute bottom-0 w-full p-3 text-white z-20 font-semibold text-lg"> Software</div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="rounded-md overflow-hidden relative w-full h-36">
                                            <div class="absolute w-full h-3/4 -bottom-12 bg-gradient-to-b from-transparent to-gray-800 z-10">
                                            </div>
                                            <img src="https://placebeard.it/400?random=4" class="absolute w-full h-full object-cover" alt="">
                                            <div class="absolute bottom-0 w-full p-3 text-white z-20 font-semibold text-lg"> Music </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="rounded-md overflow-hidden relative w-full h-36">
                                            <div class="absolute w-full h-3/4 -bottom-12 bg-gradient-to-b from-transparent to-gray-800 z-10">
                                            </div>
                                            <img src="https://placebeard.it/400?random=5" class="absolute w-full h-full object-cover" alt="">
                                            <div class="absolute bottom-0 w-full p-3 text-white z-20 font-semibold text-lg"> Travel </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="rounded-md overflow-hidden relative w-full h-36">
                                            <div class="absolute w-full h-3/4 -bottom-12 bg-gradient-to-b from-transparent to-gray-800 z-10">
                                            </div>
                                            <img src="https://placebeard.it/400?random=6" class="absolute w-full h-full object-cover" alt="">
                                            <div class="absolute bottom-0 w-full p-3 text-white z-20 font-semibold text-lg"> Development </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            
                            <a class="absolute bg-white top-16 flex items-center justify-center p-2 -left-4 rounded-full shadow-md text-xl w-9 z-10 dark:bg-gray-800 dark:text-white" href="#" uk-slider-item="previous"> <i class="icon-feather-chevron-left"></i></a>
                            <a class="absolute bg-white top-16 flex items-center justify-center p-2 -right-4 rounded-full shadow-md text-xl w-9 z-10 dark:bg-gray-800 dark:text-white" href="#" uk-slider-item="next"> <i class="icon-feather-chevron-right"></i></a>
        
                        </div>

                    </div>
    
                    <div>

                        <div class="md:flex justify-between items-center mb-8 pt-4 border-t">
    
                            <div>
                                <div class="text-xl font-semibold">Ontdek onze experten</div>
                                <div class="text-sm mt-2 font-medium text-gray-500 leading-6">Kies hier je expert uit meer dan 300 experten!</div>
                            </div>
        
                            <div class="flex items-center justify-end">
                                <div class="w-40 lg:block hidden ml-3">
                                    <select class="selectpicker is-small rounded-md shadow-sm" data-size="7">
                                        <option value="">Nieuwste</option>
                                        <option value="1">Populair</option>
                                    </select>
                                </div>
        
                            </div>
                        </div>
                      
                        <!-- course list -->
                        <div class="grid lg:grid-cols-4 md:grid-cols-2 gap-5">
	                        
	                        <?php 
		                        $experts = array(
			                        'Alain Albertz',
			                        'Aldo Peeters',
			                        'Anette Bohm',
			                        'Bart De Waele',
			                        'Birgit Wauters',
			                        'Bjorn Crul',
			                        'Bjorn Van Reet',
			                        'Catherine de Dorlodot',
			                        'Chris Demeyere',
			                        'David Ducheyne',
			                        'Dieter Crappé',
			                        'Dirk Debraekeleer'
		                        );
		                    ?>
                            
                            
                            <?php foreach($experts as $key => $expert): ?>
	                            <a href="/expert/<?=Str::slug($expert);?>" class="uk-link-reset">
	                                <div class="card uk-transition-toggle">
	                                    <div class="card-media h-40">
	                                        <div class="card-media-overly"></div>
	                                        <img src="https://placebeard.it/400?random=<?=($key+100);?>" alt="" class="">
	                                    </div>
	                                    <div class="card-body p-4">
	                                        <div class="font-semibold line-clamp-2"><?=$expert;?></div>
	                                        <small>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla ornare ut eros eu mattis.</small>
	                                        <div class="pt-1 flex items-center justify-between">
	                                            <div class="text-sm font-semibold">   </div>
	                                            <div class="text-lg font-semibold">&euro; 200/uur</div>
	                                        </div>
	                                    </div>
	                                </div>
	                            </a>
                            <?php endforeach; ?>
                        </div>
    
                        <!-- Pagination -->
                        <div class="flex justify-center mt-9 space-x-2 text-base font-semibold text-gray-400 items-center">
                            <a href="#" class="py-1 px-3 bg-gray-200 rounded text-gray-600"> 1</a>
                            <a href="#" class="py-1 px-2 bg-gray-200 rounded"> 2</a>
                            <a href="#" class="py-1 px-2 bg-gray-200 rounded"> 3</a>
                            <ion-icon name="ellipsis-horizontal" class="text-lg -mb-4"></ion-icon>
                            <a href="#" class="py-1 px-2 bg-gray-200 rounded"> 12</a>
                        </div>

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

</body>

</html>
