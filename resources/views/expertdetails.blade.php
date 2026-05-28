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
 
            <div class="lg:flex lg:space-x-10 bg-white rounded-md shadow max-w-3x  mx-auto md:p-8 p-3">
                <div class="lg:w-1/3 w-full">

                    <div class="md:block flex space-x-4">
                        <div>
                            <a href="https://placebeard.it/300x390">
                                <img alt="" src="https://placebeard.it/300x390" class="shadow-lg rounded-md w-32 md:w-full">
                            </a>                                         
                        </div>
                        <br><br>
                        <div style="margin: 0px;">
                            <div>
                                <a href="#book-me" class="hover:text-gray-800 bg-gray-300 font-semibold inline-flex items-center justify-center px-4 py-2 rounded-md text-center w-full"> <i class="uil-shopping mr-1 md:block hidden"></i> Boek deze expert </a>
                            </div>
                        </div>

                    </div><div class="uk-sticky-placeholder" style="height: 501px; margin: 0px;" hidden=""></div>
                     
                </div>
                <div class="lg:w-2/3 flex-shrink-0 mt-10 lg:m-0"> 
                    
                    <div> 

                        <h2 class="font-semibold mb-3 text-xl lg:text-3xl"><?=Str::title(str_replace('-', ' ', Request::segment(2)));?></h2>
                        <ul class="categories">
                        	<li>Business Case Modelling</li>
                        	<li>Business Controlling</li>
                        	<li>Financial Toolings / ERP</li>
                        	<li>KPI / OKR / ESG</li>
                        	<li>Startup & Scaling</li>
                        </ul>
                        <hr class="mb-5">
                        <h4 class="font-semibold mb-2 text-base">Xpert Corporate Finance</h4>   
                        <div class="space-y-2">

                            <p>Alain Albertz is a great expert to provide a second opinion because he is a passionate finance professional with a no-nonsense and can-do mentality. He specializes in helping start-ups and scale-ups become more professional organizations by supporting growth both organically and by acquisitions, post-merger integration, and setting up communication with investors, banks, and shareholders.</p>p>

<p>His expertise in finance and his experience in working with start-ups and scale-ups make him an ideal candidate to provide a second opinion. His passion for finance and his no-nonsense approach to business make him an inspiring figure in the world of finance. He is someone who can help guide businesses through the complexities of finance and help them achieve their goals. Alain Albertz is someone who can be trusted to provide sound advice and guidance to businesses of all sizes.</p>
                            
                        </div>
                          
                    </div>

                                   
                </div>
                
            </div> 
            
            <div class="lg:flex lg:space-x-10 bg-white rounded-md shadow max-w-3x  mx-auto md:p-8 p-3 mt-10" id="book-me">
                <div class="w-full flex-shrink-0 mt-10 lg:m-0"> 
                    
                    <div> 

                        <h2 class="font-semibold mb-3 text-xl lg:text-3xl">Boek deze expert</h2>
                        <hr class="mb-5">
                        <div class="space-y-2">
					        <div class="steps">
					            <div class="step" data-step="1">
					            	<h4 class="font-semibold mb-2 text-base">Selecteer je categorie</h4>					                
					                <div class="grid lg:grid-cols-1 md:grid-cols-1">
				                        <div class="item glyph fs1">
				                             <div class="clearfix bshadow0 pbs">
				                                <span><img src="https://xpertopinion.be/wp-content/uploads/booknetic/services/e2ee4fbde3cf8b8b1c026c7bd15a326c.png"></span>
				                                <div class="mls">
					                                <strong>Finance</strong>
					                            </div>
				                            </div>
				                        </div>
				                        
				                        <div class="item glyph fs1">
				                             <div class="clearfix bshadow0 pbs">
				                                <span><img src="https://xpertopinion.be/wp-content/uploads/booknetic/services/e2ee4fbde3cf8b8b1c026c7bd15a326c.png"></span>
				                                <div class="mls">
					                                <strong>Strategy</strong>
					                            </div>
				                            </div>
				                        </div>	
					                </div>
					            </div>
					            <div class="step hidden" data-step="2">
					                <h4 class="font-semibold mb-2 text-base">Selecteer je type</h4>
					                <div class="grid lg:grid-cols-1 md:grid-cols-1">
				                        <div class="item glyph fs1">
				                             <div class="clearfix bshadow0 pbs">
				                                <span><img src="https://xpertopinion.be/wp-content/uploads/booknetic/services/e2ee4fbde3cf8b8b1c026c7bd15a326c.png"></span>
				                                <div class="mls">
					                                <strong>PRIME (1u)</strong>
					                                <p>1 uur 1-on-1 gesprek</p>
					                            </div>
				                             </div>
				                         </div>	
				                         
				                         <div class="item glyph fs1">
				                             <div class="clearfix bshadow0 pbs">
				                                <span><img src="https://xpertopinion.be/wp-content/uploads/booknetic/services/e2ee4fbde3cf8b8b1c026c7bd15a326c.png"></span>
				                                <div class="mls">
					                                <strong>PRO (1u)</strong>
					                                <p>1 uur 1-on-1 gesprek en 1u voorbereiding</p>
					                            </div>
				                             </div>
				                         </div>	
				                         
				                         <div class="item glyph fs1">
				                             <div class="clearfix bshadow0 pbs">
				                                <span><img src="https://xpertopinion.be/wp-content/uploads/booknetic/services/e2ee4fbde3cf8b8b1c026c7bd15a326c.png"></span>
				                                <div class="mls">
					                                <strong>PRO+ (1u)</strong>
					                                <p>1 uur gesprek met team en 1u voorbereiding</p>
					                            </div>
				                             </div>
				                         </div>
				                         <br>	                      
				                    </div>
					            </div>
					            <div class="step hidden" data-step="3">
					                <div class="md:grid md:grid-cols-2 md:divide-x md:divide-gray-200">
									  <div class="md:pr-14">
									    <div class="flex items-center">
									      <h2 class="flex-auto text-sm font-semibold text-gray-900">November 2023</h2>
									      <button type="button" class="-my-1.5 flex flex-none items-center justify-center p-1.5 text-gray-400 hover:text-gray-500">
									        <span class="sr-only">Vorige maand</span>
									        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
									          <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
									        </svg>
									      </button>
									      <button type="button" class="-my-1.5 -mr-1.5 ml-2 flex flex-none items-center justify-center p-1.5 text-gray-400 hover:text-gray-500">
									        <span class="sr-only">Volgende maand</span>
									        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
									          <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
									        </svg>
									      </button>
									    </div>
									    <div class="mt-10 grid grid-cols-7 text-center text-xs leading-6 text-gray-500">
									      <div>Ma</div>
									      <div>Di</div>
									      <div>Wo</div>
									      <div>Do</div>
									      <div>Vr</div>
									      <div>Za</div>
									      <div>Zo</div>
									    </div>
									    <div class="mt-2 grid grid-cols-7 text-sm">
									      <div class="py-2">
									        <!--
									          Always include: "mx-auto flex h-8 w-8 items-center justify-center rounded-full"
									          Is selected, include: "text-white"
									          Is not selected and is today, include: "text-indigo-600"
									          Is not selected and is not today and is current month, include: "text-gray-900"
									          Is not selected and is not today and is not current month, include: "text-gray-400"
									          Is selected and is today, include: "bg-indigo-600"
									          Is selected and is not today, include: "bg-gray-900"
									          Is not selected, include: "hover:bg-gray-200"
									          Is selected or is today, include: "font-semibold"
									        -->
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-400 hover:bg-gray-200">
									          <time datetime="2021-12-27">27</time>
									        </button>
									      </div>
									      <div class="py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-400 hover:bg-gray-200">
									          <time datetime="2021-12-28">28</time>
									        </button>
									      </div>
									      <div class="py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-400 hover:bg-gray-200">
									          <time datetime="2021-12-29">29</time>
									        </button>
									      </div>
									      <div class="py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-400 hover:bg-gray-200">
									          <time datetime="2021-12-30">30</time>
									        </button>
									      </div>
									      <div class="py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-400 hover:bg-gray-200">
									          <time datetime="2021-12-31">31</time>
									        </button>
									      </div>
									      <div class="py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-900 hover:bg-gray-200">
									          <time datetime="2022-01-01">1</time>
									        </button>
									      </div>
									      <div class="py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-900 hover:bg-gray-200">
									          <time datetime="2022-01-02">2</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-900 hover:bg-gray-200">
									          <time datetime="2022-01-03">3</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-900 hover:bg-gray-200">
									          <time datetime="2022-01-04">4</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-900 hover:bg-gray-200">
									          <time datetime="2022-01-05">5</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-900 hover:bg-gray-200">
									          <time datetime="2022-01-06">6</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-900 hover:bg-gray-200">
									          <time datetime="2022-01-07">7</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-900 hover:bg-gray-200">
									          <time datetime="2022-01-08">8</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-900 hover:bg-gray-200">
									          <time datetime="2022-01-09">9</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-900 hover:bg-gray-200">
									          <time datetime="2022-01-10">10</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-900 hover:bg-gray-200">
									          <time datetime="2022-01-11">11</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full font-semibold text-indigo-600 hover:bg-gray-200">
									          <time datetime="2022-01-12">12</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-900 hover:bg-gray-200">
									          <time datetime="2022-01-13">13</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-900 hover:bg-gray-200">
									          <time datetime="2022-01-14">14</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-900 hover:bg-gray-200">
									          <time datetime="2022-01-15">15</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-900 hover:bg-gray-200">
									          <time datetime="2022-01-16">16</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-900 hover:bg-gray-200">
									          <time datetime="2022-01-17">17</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-900 hover:bg-gray-200">
									          <time datetime="2022-01-18">18</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-900 hover:bg-gray-200">
									          <time datetime="2022-01-19">19</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-900 hover:bg-gray-200">
									          <time datetime="2022-01-20">20</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full bg-gray-900 font-semibold text-white">
									          <time datetime="2022-01-21">21</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-900 hover:bg-gray-200">
									          <time datetime="2022-01-22">22</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-900 hover:bg-gray-200">
									          <time datetime="2022-01-23">23</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-900 hover:bg-gray-200">
									          <time datetime="2022-01-24">24</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-900 hover:bg-gray-200">
									          <time datetime="2022-01-25">25</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-900 hover:bg-gray-200">
									          <time datetime="2022-01-26">26</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-900 hover:bg-gray-200">
									          <time datetime="2022-01-27">27</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-900 hover:bg-gray-200">
									          <time datetime="2022-01-28">28</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-900 hover:bg-gray-200">
									          <time datetime="2022-01-29">29</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-900 hover:bg-gray-200">
									          <time datetime="2022-01-30">30</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-900 hover:bg-gray-200">
									          <time datetime="2022-01-31">31</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-400 hover:bg-gray-200">
									          <time datetime="2022-02-01">1</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-400 hover:bg-gray-200">
									          <time datetime="2022-02-02">2</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-400 hover:bg-gray-200">
									          <time datetime="2022-02-03">3</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-400 hover:bg-gray-200">
									          <time datetime="2022-02-04">4</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-400 hover:bg-gray-200">
									          <time datetime="2022-02-05">5</time>
									        </button>
									      </div>
									      <div class="border-t border-gray-200 py-2">
									        <button type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-400 hover:bg-gray-200">
									          <time datetime="2022-02-06">6</time>
									        </button>
									      </div>
									    </div>
									  </div>
									  <section class="mt-12 md:mt-0 md:pl-14 calendar">
									    <h2 class="text-base font-semibold leading-6 text-gray-900">Boek deze expert op <time datetime="2022-01-21">21 November 2023</time></h2>
									    <ol class="mt-4 space-y-1 text-sm leading-6 text-gray-500">
									      <li class="group flex items-center space-x-4 rounded-xl px-4 py-2 focus-within:bg-gray-100 hover:bg-gray-100" style="cursor: pointer;">
									        <img src="https://placebeard.it/300x390" alt="" class="h-10 w-10 flex-none rounded-full">
									        <div class="flex-auto">
									          <p class="mt-0.5"><time datetime="2022-01-21T13:00">09:00</time> - <time datetime="2022-01-21T14:30">10:00</time></p>
									          <p class="text-gray-900" style="margin-top: -10px;"><small style="font-weight: bold;">Boek expert</small></p>
									        </div>
									      </li>
									
									      <!-- More meetings... -->
									    </ol>
									  </section>
									</div>

					            </div>
					            
					            <div class="step hidden" data-step="4">
					                <h4 class="font-semibold mb-2 text-base">Vul je gegevens in</h4>
					                <a href="/expert/payment" style="padding: 5px 10px; background: #ff3665; border-radius: 10px;">Betaal hier</a>
					            </div>
					        </div>
					        <div class="flex justify-between buttons">
					            <button class="btn-prev bg-gray-400 px-4 py-2 rounded">Vorige</button>
					            <button class="btn-next bg-blue-500 text-white px-4 py-2 rounded">Volgende</button>
					        </div>
					    </div>
                          
                    </div>

                                   
                </div>
                
            </div> 

            <!--  books  -->
            <div class="sm:my-4 my-3 flex items-end justify-between pt-3 px-3 md:px-0">
                <h2 class="text-2xl font-semibold"> Gerelateerde experts </h2>
                <a href="#" class="text-blue-500 sm:block hidden"> Bekijk allemaal </a>
            </div>
            <div class="relative px-3 md:p-0 uk-slider" uk-slider="finite: true">
                <div class="uk-slider-container px-1 py-3">
                    <ul class="uk-slider-items uk-child-width-1-5@m uk-child-width-1-3@s uk-child-width-1-2 uk-grid-small uk-grid text-sm font-medium text-center" style="transform: translate3d(0px, 0px, 0px);">
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
	                        <li tabindex="-1" class="uk-active">
	                            <div class="card">
	                                <a href="book-description.html">
	                                    <img src="https://placebeard.it/400?random=<?=$key;?>" alt="" class="w-full h-52 object-cover">
	                                    <div class="p-3 truncate"><?=$expert;?></div>
	                                </a>
	                            </div>
	                        </li>
                        <?php endforeach; ?>
                    </ul>
            
                    <a class="absolute bg-white bottom-1/2 flex items-center justify-center p-2 -left-4 rounded-full shadow-md text-xl w-9 z-10 dark:bg-gray-800 dark:text-white uk-invisible" href="#" uk-slider-item="previous"> <i class="icon-feather-chevron-left"></i></a>
                    <a class="absolute bg-white bottom-1/2 flex items-center justify-center p-2 -right-4 rounded-full shadow-md text-xl w-9 z-10 dark:bg-gray-800 dark:text-white" href="#" uk-slider-item="next"> <i class="icon-feather-chevron-right"></i></a>
            
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
