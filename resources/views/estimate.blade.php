<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Estimate - Detail</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="google-site-verification" content="wplJ76MFE1jQD7vAoJpceEawVYWfctmb0QtDD49HI5o" />
    <meta name="robots" content="noindex, nofollow, noarchive">
    <link href="/assets_estimate/style.css" rel="stylesheet">
    
    <style>
        #signature-pad {
		    width: 100%;
		    height: 200px;
		    background: #fff;
		    border: 1px solid #ece9e9;
		    border-radius: 15px;
        }
        #signature-pad canvas {
            width: 100%;
            height: 100%;
        }
        
        .refuse-btn {
	        opacity: 0.5;
        }
        
        .refuse-btn:hover {
	        opacity: 1;
        }
        
        small.extrainfo {
	        font-weight: bold;
	        font-style: italic;
	        color: #ff3665;
	        position: relative;
	        top: -10px;
	        left: 10px;
        }
        
        .mt-10 {
	        margin-top: 25px;
        }
        
        .signatures-container {
	        display: flex !important;
	        flex-direction: row !important;
	        gap: 40px !important;
	        align-items: flex-start !important;
	        width: 100% !important;
	        justify-content: center !important;
	        max-width: 1000px !important;
	        margin: 0 auto !important;
        }
        
        .signature-box {
	        flex: 0 1 45% !important;
	        min-width: 350px !important;
	        max-width: 450px !important;
        }
        
        @media (max-width: 768px) {
	        .signatures-container {
		        flex-direction: column !important;
	        }
	        .signature-box {
		        width: 100% !important;
	        }
        }
        
        <?php if($booking->accepted && $booking->customer_accepted == 0 && $booking->customer_refused == 0): ?>
	        .hidesig {
	        }
        <?php endif; ?>
	        
		.btn {
		    padding: 5px 10px;
		    border: none;
		    cursor: pointer;
		    font-weight: bold;
		    border-radius: 5px;
		    transition: background-color 0.3s, opacity 0.3s;
		}
		
		.yes-button, .no-button {
			position: relative;
			top: -5px;
		}
		
		.yes-button {
		    background-color: #4CAF50;
		    color: white;
		}
		
		.no-button {
		    background-color: #f44336;
		    color: white;
		}
		
		.yes-button.active {
		    opacity: 1;
		}
		
		.no-button.active {
		    opacity: 1;
		}
		
		.yes-button:not(.active) {
		    opacity: 0.5;
		}
		
		.no-button:not(.active) {
		    opacity: 0.5;
		}
		
		.whitelogo {
			filter: brightness(0) invert(1);
		}
		
		@media print {
			.btn {
				display: none;
			}
			
			.noprint {
				display: none;
			}
			
			.noprintbanner {
				background-color: transparent;
				margin-top: 80px !important;
			}
			
			.noprintbanner .min-h-full.w-full.max-w-xl.mx-auto {
				padding: 0px;
				padding-left: 25px;
			}
			
			.max-w-xl {
				width: 100%;
				max-width: 90%;
			}
			
			.whitelogo {
				filter: inherit;
				height: 50px;
			}
			
			.py-12, .py-8 {
				padding-top: 15px !important;
				padding-bottom: 15px !important;
			}
			
			h1 small[style] {
				font-size: 21px !important;
			    line-height: 1.5;
			    letter-spacing: -0.01em;
				color: #1e293b !important;
			}
			
			.listspeakers {
				padding-top: 0px !important;
				margin-top: -20px !important;
			}
			
			.listspeakers li[style] {
				font-size: 16px !important;
				padding-bottom: 5px;
				color: #64768b !important;
			}
			
			.noprintmargintop {
				margin-top: 20px;
			}
			
			.noprintmb[style] {
				margin-bottom: 20px !important;
			}
			
			.mb-5 {
				margin-bottom: 10px !important;
			}
			
			.mt-10 {
				margin-top: 0px !important;
			}
			
			.py-4, .px-4, .px-5, .py-5 {
				padding: 10px 0 !important;
			}
		}
	</style>

</head>

<body class="font-inter antialiased bg-white text-slate-800 dark:bg-slate-950 dark:text-slate-200 tracking-tight">

    <!-- Page wrapper -->
    <div class="flex flex-col min-h-screen overflow-hidden">

        <!-- Site header -->
        <header class="absolute w-full z-30">
            <div class="max-w-xl lg:max-w-[calc(50%+theme(maxWidth.xl))] mx-auto px-4 sm:px-6">
                <div class="flex items-center justify-between h-16 md:h-20">
        
                    <!-- Site branding -->
                    <div class="shrink-0 mr-4 lg:fixed" style="margin-top: 30px;">
                        <!-- Logo -->
                        <a class="flex items-center space-x-4" href="#" aria-label="Cruip">
                            <img src="https://readmylips.be/themes/custom/dropsolid-theme-flex-8/logo.svg" width="150" alt="RML logo" class="whitelogo">
                        </a>
                    </div>
        
                    <!-- Right side -->
                    <div class="flex grow justify-end">
            
                        <!-- Light switch -->
                        <div class="flex flex-col justify-center">
                            
                        </div>
        
                    </div>
        
                </div>
            </div>
        </header>

        <div class="grow flex flex-col lg:flex-row">

            <!-- Left side -->
            <?php if($print == false): ?>
	            <div class="noprintbanner relative w-full lg:w-1/2 lg:fixed lg:inset-0 lg:overflow-y-auto no-scrollbar bg-slate-900 lg:rounded-r-[3rem]">
	
	                <!-- Background Illustration -->
	                <div class="noprint absolute top-0 -translate-y-64 left-1/2 -translate-x-1/2 blur-3xl pointer-events-none" aria-hidden="true">
	                    <img class="max-w-none" src="/assets_estimate/images/bg-illustration.svg" width="785" height="685" alt="Bg illustration">
	                </div>
	
	                <div class="min-h-full w-full max-w-xl mx-auto flex flex-col justify-start px-4 sm:px-6 pt-36 pb-20 lg:py-20">
	                    <div class="grow flex flex-col justify-center">
	            
	                        <div class="space-y-3">
		                        <?php if($speaker == false): ?>
	                            	<h1 class="h1 font-orbiter font-bold text-white">
		                            	<small style="font-size: 45px;">{{ __('estimate.offer_for') }} #<?=$booking->id;?></small></h1><br>
		                            	<ul class="listspeakers">
			                            	<?php foreach($booking_assets as $booking_asset): ?>
				                            	<li style="color: #fff; font-size: 24px; padding-bottom: 10px; width: 500px;">
					                            	<?=$booking_asset->title;?>
					                            	
					                            	<a class="noprint" href="https://readmylips.be/node/<?=$booking_asset->drupal_id;?>" target="_blank" style="color: #000; background: #fff; padding: 5px 10px; left: 15px; top: 0px; font-size: 14px; float: right; position: relative; border-radius: 10px; font-weight: bold;">{{ __('estimate.check_profile') }}</a>
				                            	</li>
			                            	<?php endforeach; ?>
										</ul>
	                            <?php else: ?>
	                            	<h1 class="h1 font-orbiter font-bold text-white"><small style="font-size: 45px;">Booking request #<?=$booking->id;?></small><br> <span style="color: #fff; font-size: 24px; padding-bottom: 10px; width: 500px;">By <?=$booking->invoice_name;?></span<</h1>
	                            <?php endif; ?>
	                            
	                            <a class="noprint" style="cursor: pointer;color: #fff;background: #121a2c;padding: 15px 25px;margin-right: 30px;right: 25px;bottom: 25px;z-index: 9999999999;font-size: 14px;float: right;position: fixed;border-radius: 15px;font-weight: bold;" onclick="window.print()">Print</a>

	                        </div>
	            
	                    </div>
	                </div>
	            </div>
	        <?php endif; ?>

            <!-- Right side -->
            <main class="noprintmargintop max-lg:grow flex flex-col w-full <?php if($print == false): ?>lg:w-1/2<?php endif; ?> lg:ml-auto">
	            <form method="post">
	                <!-- Page content -->
	                <div class="grow w-full max-w-xl mx-auto px-4 sm:px-6 py-12 lg:pt-24 lg:pb-40" style="margin-bottom: 0px;">
	
	                    <article class="divide-y divide-slate-100 dark:divide-slate-800 -mt-8">	                   
	                        <section class="py-8">
	                            <h2 class="text-lg font-semibold mb-2">{{ __('estimate.customer_details') }}</h2>
	                            <div style="height: 130px;">
		                            <div class="text-slate-500 dark:text-slate-400 space-y-4" style="float: left; width: 50%;">
			                            <?=$booking->invoice_company;?><br>
		                                <?=$booking->invoice_name; ?><br>
		                                <?=$booking->invoice_address; ?><br>
		                                <?=$booking->invoice_postal; ?> <?=$booking->invoice_city; ?><br>
		                                <?=$booking->invoice_vat; ?><br>
		                                <?=$booking->invoice_email; ?>
		                            </div>
		                            <div class="text-slate-500 dark:text-slate-400 space-y-4" style="float: left; width: 50%;">
		                                Read My Lips<br>
										Brasschaatsesteenweg 308<br>
										2920 Kalmthout<br>
										<?=$customer_settings->extra_info_invoice;?>
		                            </div>
	                            </div>
	                        </section>
	                        
		                    <?php if($booking->remark <> ''): ?>
		                        <section class="py-8">
		                            <h2 class="text-lg font-semibold mb-2">{{ __('estimate.description') }}</h2>
		                            <div class="text-slate-500 dark:text-slate-400 space-y-4">
		                                <?=$booking->remark; ?>
		                            </div>
		                        </section>
		                    <?php endif; ?>
		                    
	                        <section class="py-8">
	                            <h2 class="text-lg font-semibold mb-5">Details</h2>
	                            <ul class="grid gap-4 min-[480px]:grid-cols-3 text-sm">
	                                <li class="px-5 py-4 rounded-lg bg-gradient-to-tr from-slate-950 to-slate-800 dark:from-slate-800/80 dark:to-slate-900">
	                                    <div class="text-slate-200 font-medium">{{ __('estimate.location') }}</div>
	                                    <div class="text-slate-400"><?=ucfirst($booking->location); ?></div>
	                                </li>
	                                <li class="px-5 py-4 rounded-lg bg-gradient-to-tr from-slate-950 to-slate-800 dark:from-slate-800/80 dark:to-slate-900">
	                                    <div class="text-slate-200 font-medium">{{ __('estimate.date') }}</div>
	                                    <time class="text-slate-400"><?=($booking->date)?date('d-m-Y', strtotime($booking->date)):'Onbekend'; ?></time>
	                                </li>
	                                <li class="px-5 py-4 rounded-lg bg-gradient-to-tr from-slate-950 to-slate-800 dark:from-slate-800/80 dark:to-slate-900">
	                                    <div class="text-slate-200 font-medium">{{ __('estimate.time') }}</div>
	                                    <time class="text-slate-400"><?=$booking->date_hour_start; ?> - <?=$booking->date_hour_end; ?></time>
	                                </li>
	                            </ul>
	                        </section>
	                        
	                        <section class="py-8">
							    <?php $total = 0; ?>
							    <?php foreach($booking_assets as $booking_asset): ?>
							        <h2 class="text-lg font-semibold mt-10" style="margin-bottom: 10px; position: relative;">
								        <?php if($speaker): ?>
								        	{{ __('estimate.your_income') }}
								        <?php else: ?>
							            	<span><?=$booking_asset->title;?></span>
							            <?php endif; ?>
							            
							            <?php if(!$speaker && $booking->accepted == 0): ?>
							            	<?php $total_line = 0; ?>
							            	<?php foreach(json_decode($booking_asset->estimate) as $key => $line): ?>                                    
						                        <?php if($speaker == false || $speaker && isset($line->visible_speaker) && $line->visible_speaker): ?>
						                            <?php if(isset($line->add_markup) && !$speaker && $line->add_markup): ?>
						                                <?php
						                                    if($customer_settings->markup_type == 2) {
							                                    if(isset($line->markup)) {
						                                        	$line->value = $line->value/100*(100+$line->markup);
						                                        } else {
						                                        	$line->value = $line->value/100*(100+$customer_settings->markup);
						                                        }
						                                    } else {
							                                    if(isset($line->markup)) {
						                                        	$line->value = $line->value+$line->markup;
						                                        } else {
						                                        	$line->value = $line->value+$customer_settings->markup;
						                                        }
						                                    }
						                                ?>
						                            <?php endif; ?>
						                            <?php $total_line += $line->value; ?>
						                        <?php endif; ?>
						                    <?php endforeach; ?>
						                    
								            <span style="position: absolute; right: 0px; background: #fff;">
									            <!-- Yes Button -->
												<button class="yes-button btn active" data-table="asset-table-<?=$booking_asset->asset_id;?>" data-value="<?=$total_line;?>" style="margin-right: 10px;">{{ __('estimate.accept') }}</button>
												
												<!-- No Button -->
												<button class="no-button btn" data-table="asset-table-<?=$booking_asset->asset_id;?>" data-value="<?=$total_line;?>">{{ __('estimate.revoke') }}</button>
												
												<!-- Checkbox -->
												<input type="checkbox" id="checkbox-<?=$booking_asset->asset_id;?>" name="booking_assets[<?=$booking_asset->asset_id;?>]" checked value="1" style="display: none;">
								            </span>
							            <?php endif; ?>
							        </h2>
							        <div class="overflow-x-auto noprintmb" style="border-radius: 10px; background: #f4f7fa; margin-bottom: 50px;">
							            <table class="table-auto w-full text-sm asset-table-<?=$booking_asset->id;?>">
							                <thead class="sr-only">
							                    <tr>
							                        <th>{{ __('estimate.description') }}</th>
							                        <th scope="col" style="width: 250px;">{{ __('estimate.cost') }}</th>
							                    </tr>
							                </thead>
							                <tbody>
							                    <?php foreach(json_decode($booking_asset->estimate) as $key => $line): ?>                                    
							                        <?php if($speaker == false || $speaker && isset($line->visible_speaker) && $line->visible_speaker): ?>
							                            <?php if(isset($line->add_markup) && !$speaker && $line->add_markup): ?>
							                                <?php
							                                    if($customer_settings->markup_type == 2) {
								                                    if(isset($line->markup)) {
							                                        	$line->value = $line->value/100*(100+$line->markup);
							                                        } else {
							                                        	$line->value = $line->value/100*(100+$customer_settings->markup);
							                                        }
							                                    } else {
								                                    if(isset($line->markup)) {
							                                        	$line->value = $line->value+$line->markup;
							                                        } else {
							                                        	$line->value = $line->value+$customer_settings->markup;
							                                        }
							                                    }
							                                ?>
							                            <?php endif; ?>
							                            <?php $total += $line->value; ?>
							                            <tr class="group from-slate-100 to-slate-50 dark:from-slate-800/80 dark:to-slate-900">
							                                <th scope="row" class="relative text-left font-normal px-4 py-5 first:rounded-l-lg last:rounded-r-lg after:w-px after:h-8 after:bg-slate-200 dark:after:bg-slate-800 after:absolute after:right-0 after:top-1/2 after:-translate-y-1/2 group-hover:after:opacity-0 after:transition-opacity">
							                                    <div class="font-semibold mb-0.5">
							                                        <?=$line->line;?>
							                                    </div>
							                                </th>
							                                <td class="relative font-semibold text-right px-4 py-5 first:rounded-l-lg last:rounded-r-lg w-[1%]" style="min-width: 140px;">
							                                    &euro; <?=number_format($line->value, 2, ',', '.');?>
							                                </td>
							                            </tr>
							                        <?php endif; ?>
							                    <?php endforeach; ?>
							                </tbody>
							            </table>
							        </div>
							    <?php endforeach; ?>
							    
							    <div class="overflow-x-auto">
							        <table class="table-auto w-full text-sm main-table">
							            <tbody>
							                <?php foreach(json_decode($booking->customer_estimate_lines) as $key => $line): ?>                                    
							                    <?php if($speaker == false || $speaker && isset($line->visible_speaker) && $line->visible_speaker): ?>
							                        <?php if(isset($line->add_markup) && !$speaker && $line->add_markup): ?>
							                            <?php
							                                if($customer_settings->markup_type == 2) {
							                                    $line->value = $line->value/100*(100+$customer_settings->markup);
							                                } else {
							                                    $line->value = $line->value+$customer_settings->markup;
							                                }
							                            ?>
							                        <?php endif; ?>
							                        <?php $total += $line->value; ?>
							                        <tr class="group odd:bg-gradient-to-tr from-slate-100 to-slate-50 dark:from-slate-800/80 dark:to-slate-900">
							                            <th scope="row" class="relative text-left font-normal px-4 py-5 first:rounded-l-lg last:rounded-r-lg after:w-px after:h-8 after:bg-slate-200 dark:after:bg-slate-800 after:absolute after:right-0 after:top-1/2 after:-translate-y-1/2 group-hover:after:opacity-0 after:transition-opacity">
							                                <div class="font-semibold mb-0.5">
							                                    <?=$line->line;?>
							                                </div>
							                            </th>
							                            <td class="relative font-semibold text-right px-4 py-5 first:rounded-l-lg last:rounded-r-lg w-[1%]" style="min-width: 140px;">
							                                &euro; <?=number_format($line->value, 2, ',', '.');?>
							                            </td>
							                        </tr>
							                    <?php endif; ?>
							                <?php endforeach; ?>
							            </tbody>
							        </table>
							    </div>
							
							    <div class="overflow-x-auto mt-10">
							        <table class="table-auto w-full text-sm">
							            <tfoot>
							                <tr>
							                    <th scope="row" class="relative text-left font-normal px-4 py-5">
							                        <p class="text-slate-500 italic">{{ __('estimate.total_excl') }}</p>
							                    </th>
							                    <td class="relative font-semibold text-right text-emerald-500 text-base underline px-4 py-5 w-[1%] total-price" style="width: 200px;">&euro; <?=number_format($total, 2, ',', '.');?></td>
							                </tr>
							                <tr>
							                    <th scope="row" class="relative text-left font-normal px-4 py-5">
							                        <p class="text-slate-500 italic">{{ __('estimate.total_incl') }}</p>
							                    </th>
							                    <td class="relative font-semibold text-right text-emerald-500 text-base underline px-4 py-5 w-[1%] total-price-incl" style="width: 200px;">&euro; <?=number_format(($total*1.21), 2, ',', '.');?></td>
							                </tr>
							            </tfoot>
							        </table>
							    </div>
							</section>
		                    
		                    <?php if($booking->customer_signature): ?>
		                    	<section class="py-8" style="border-top: 2px solid #e2e8f0; margin-top: 30px; padding-top: 30px;">
		                    		<h2 class="text-lg font-semibold mb-6" style="color: #1e293b; font-size: 20px;">Signatures</h2>
		                    		
		                    		<!-- Signatures Side by Side -->
		                    		<div class="signatures-container" style="margin-bottom: 40px;">
		                    			<!-- Customer Signature -->
		                    			<div class="signature-box">
		                    				<h3 class="text-md font-semibold mb-3" style="color: #475569; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">Customer Signature</h3>
		                    				<div style="border: 2px solid #e2e8f0; border-radius: 12px; padding: 25px; background: #f8fafc; display: flex; flex-direction: column; min-height: 280px;">
		                    					<div style="flex: 1; display: flex; align-items: center; justify-content: center; min-height: 150px; background: #fff; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
		                    						<img style="max-width: 100%; max-height: 150px; display: block; object-fit: contain;" src="<?=$booking->customer_signature; ?>" alt="Customer signature" />
		                    					</div>
		                    					<?php if($booking->last_action_customer): ?>
		                    						<div style="border-top: 1px solid #cbd5e1; padding-top: 15px; margin-top: auto;">
		                    							<p style="font-size: 12px; color: #64748b; margin: 0; font-weight: 500; text-align: center;">
		                    								<span style="color: #475569; font-weight: 600;">Signed on:</span><br>
		                    								<span style="color: #1e293b; font-size: 13px;"><?=date('d-m-Y', $booking->last_action_customer);?> at <?=date('H:i', $booking->last_action_customer);?></span>
		                    							</p>
		                    						</div>
		                    					<?php endif; ?>
		                    				</div>
		                    			</div>
		                    			
		                    			<!-- RML Counter-Signature -->
		                    			<div class="signature-box">
		                    				<h3 class="text-md font-semibold mb-3" style="color: #475569; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">Counter-Signature</h3>
		                    				<div style="border: 2px solid #e2e8f0; border-radius: 12px; padding: 25px; background: #f8fafc; display: flex; flex-direction: column; min-height: 280px;">
		                    					<div style="flex: 1; display: flex; flex-direction: column; justify-content: flex-start; min-height: 150px; margin-bottom: 20px;">
		                    						<div style="margin-bottom: 20px;">
		                    							<p style="font-weight: 700; margin: 0 0 8px 0; color: #1e293b; font-size: 16px;">Read My Lips BV</p>
		                    							<p style="color: #64748b; font-size: 13px; margin: 0; line-height: 1.6;">
		                    								Brasschaatsesteenweg 308<br>
		                    								2920 Kalmthout<br>
		                    								Belgium
		                    							</p>
		                    						</div>
		                    					</div>
		                    					<div style="border-top: 2px solid #cbd5e1; padding-top: 15px; margin-top: auto;">
		                    						<p style="font-size: 12px; color: #64748b; margin: 0; font-weight: 500; text-align: center;">
		                    							<span style="color: #475569; font-weight: 600;">Authorized signature</span>
		                    						</p>
		                    					</div>
		                    				</div>
		                    			</div>
		                    		</div>
		                    		
		                    		<!-- Filled Form Information -->
		                    		<?php if($booking->extra_info_json && !$speaker): ?>
		                    			<section class="py-8">
		                    				<h2 class="text-lg font-semibold mb-5">{{ __('estimate.estimate_extra_info') }}</h2>
		                    				<?php $extra_info = json_decode($booking->extra_info_json); ?>
		                    				
		                    				<h3 class="text-lg font-semibold mb-2" style="margin-top: 20px;">{{ __('estimate.estimate_extra_info_1') }}</h3>
		                    				<div class="overflow-x-auto" style="border-radius: 10px; background: #f4f7fa; margin-bottom: 25px;">
		                    					<table class="table-auto w-full text-sm">
		                    						<tbody>
		                    							<?php 
		                    								$fields_section1 = array(
		                    									'date_prep_meeting',
		                    									'date_keynote',
		                    									'content_keynote',
		                    									'language_keynote',
		                    									'amount_profile_audience',
		                    									'hour_to_arrive',
		                    									'technical_rehearsal',
		                    									'keynote_from_to',
		                    									'address',
		                    									'parking_info',
		                    									'contact_info_locally'
		                    								);
		                    								$visible_fields1 = array_filter($fields_section1, function($f) use ($extra_info) { return isset($extra_info->$f) && $extra_info->$f; });
		                    								$chunks = array_chunk($visible_fields1, 3);
		                    							?>
		                    							<?php foreach($chunks as $chunk): ?>
		                    								<tr class="group odd:bg-gradient-to-tr from-slate-100 to-slate-50 dark:from-slate-800/80 dark:to-slate-900">
		                    									<?php foreach($chunk as $field): ?>
		                    										<th scope="row" class="relative text-left font-normal px-4 py-4 first:rounded-l-lg last:rounded-r-lg after:w-px after:h-8 after:bg-slate-200 dark:after:bg-slate-800 after:absolute after:right-0 after:top-1/2 after:-translate-y-1/2 group-hover:after:opacity-0 after:transition-opacity" style="width: 33.33%; vertical-align: top; text-align: left;">
		                    											<div class="text-slate-500 dark:text-slate-400 font-medium mb-2" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; text-align: left;"><?= __('estimate.' . $field) ?></div>
		                    											<div class="font-semibold" style="color: #1e293b; text-align: left; font-size: 14px; line-height: 1.5;"><?= htmlspecialchars($extra_info->$field, ENT_QUOTES, 'UTF-8'); ?></div>
		                    										</th>
		                    									<?php endforeach; ?>
		                    									<?php if(count($chunk) == 1): ?>
		                    										<td class="relative px-4 py-4" style="width: 33.33%;"></td>
		                    										<td class="relative px-4 py-4" style="width: 33.33%;"></td>
		                    									<?php elseif(count($chunk) == 2): ?>
		                    										<td class="relative px-4 py-4" style="width: 33.33%;"></td>
		                    									<?php endif; ?>
		                    								</tr>
		                    							<?php endforeach; ?>
		                    						</tbody>
		                    					</table>
		                    				</div>
		                    				
		                    				<h3 class="text-lg font-semibold mb-2">{{ __('estimate.estimate_extra_info_2') }}</h3>
		                    				<div class="overflow-x-auto" style="border-radius: 10px; background: #f4f7fa;">
		                    					<table class="table-auto w-full text-sm">
		                    						<tbody>
		                    							<?php 
		                    								$fields_section2 = array(
		                    									'vat_number',
		                    									'invoicing_address',
		                    									'invoice_email',
		                    									'po_ref_number',
		                    									'payment_time_end_month'
		                    								);
		                    								$visible_fields2 = array_filter($fields_section2, function($f) use ($extra_info) { return isset($extra_info->$f) && $extra_info->$f; });
		                    								$chunks = array_chunk($visible_fields2, 3);
		                    							?>
		                    							<?php foreach($chunks as $chunk): ?>
		                    								<tr class="group odd:bg-gradient-to-tr from-slate-100 to-slate-50 dark:from-slate-800/80 dark:to-slate-900">
		                    									<?php foreach($chunk as $field): ?>
		                    										<th scope="row" class="relative text-left font-normal px-4 py-4 first:rounded-l-lg last:rounded-r-lg after:w-px after:h-8 after:bg-slate-200 dark:after:bg-slate-800 after:absolute after:right-0 after:top-1/2 after:-translate-y-1/2 group-hover:after:opacity-0 after:transition-opacity" style="width: 33.33%; vertical-align: top; text-align: left;">
		                    											<div class="text-slate-500 dark:text-slate-400 font-medium mb-2" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; text-align: left;"><?= __('estimate.' . $field) ?></div>
		                    											<div class="font-semibold" style="color: #1e293b; text-align: left; font-size: 14px; line-height: 1.5;"><?= htmlspecialchars($extra_info->$field, ENT_QUOTES, 'UTF-8'); ?></div>
		                    										</th>
		                    									<?php endforeach; ?>
		                    									<?php if(count($chunk) == 1): ?>
		                    										<td class="relative px-4 py-4" style="width: 33.33%;"></td>
		                    										<td class="relative px-4 py-4" style="width: 33.33%;"></td>
		                    									<?php elseif(count($chunk) == 2): ?>
		                    										<td class="relative px-4 py-4" style="width: 33.33%;"></td>
		                    									<?php endif; ?>
		                    								</tr>
		                    							<?php endforeach; ?>
		                    						</tbody>
		                    					</table>
		                    				</div>
		                    			</section>
		                    		<?php endif; ?>
		                    	</section>
		                    <?php endif; ?>
	                        
		                    <?php if($booking->estimate_extra_details <> '' && !$speaker): ?>
		                        <section class="py-8">
		                            <h2 class="text-lg font-semibold mb-2">{{ __('estimate.estimate_extra_details') }}</h2>
		                            <div class="text-slate-500 dark:text-slate-400 space-y-4">
		                                <?=$booking->estimate_extra_details; ?>
		                            </div>
		                        </section>
		                    <?php endif; ?>
							
							<?php if($booking->accepted && $booking->customer_accepted == 0 && $booking->customer_refused == 0 && !$speaker): ?>
								<section class="noprint">
									<br>
		                            <h2 class="text-lg font-semibold mb-2">{{ __('estimate.estimate_extra_info') }}</h2>
		                            <div class="text-slate-500 dark:text-slate-400 space-y-4">
		                                {{ __('estimate.estimate_extra_info_text') }}
		                                
										<h3 class="text-lg font-semibold mb-2 mt-2" style="font-size: 18px;">
											{{ __('estimate.estimate_extra_info_1') }}
										</h3>
										
										<input required name="extra_info[date_prep_meeting]" class="extrainfofield" placeholder="{{ __('estimate.date_prep_meeting') }}" value="" style="width: 100%; margin-top: 10px; border: 0px; background: #fff; border: 1px solid #ece9e9; border-radius: 15px; padding: 10px; font-size: 14px;">

<input required name="extra_info[date_keynote]" placeholder="{{ __('estimate.date_keynote') }}" class="extrainfofield" value="" style="width: 100%; margin-top: 10px; border: 0px; background: #fff; border: 1px solid #ece9e9; border-radius: 15px; padding: 10px; font-size: 14px;">

<input required name="extra_info[content_keynote]" placeholder="{{ __('estimate.content_keynote') }}" class="extrainfofield" value="" style="width: 100%; margin-top: 10px; border: 0px; background: #fff; border: 1px solid #ece9e9; border-radius: 15px; padding: 10px; font-size: 14px;">

<input required name="extra_info[language_keynote]" placeholder="{{ __('estimate.language_keynote') }}" class="extrainfofield" value="" style="width: 100%; margin-top: 10px; border: 0px; background: #fff; border: 1px solid #ece9e9; border-radius: 15px; padding: 10px; font-size: 14px;">

<input required name="extra_info[amount_profile_audience]" placeholder="{{ __('estimate.amount_profile_audience') }}" class="extrainfofield" value="" style="width: 100%; margin-top: 10px; border: 0px; background: #fff; border: 1px solid #ece9e9; border-radius: 15px; padding: 10px; font-size: 14px;">

<input required name="extra_info[hour_to_arrive]" placeholder="{{ __('estimate.hour_to_arrive') }}" class="extrainfofield" value="" style="width: 100%; margin-top: 10px; border: 0px; background: #fff; border: 1px solid #ece9e9; border-radius: 15px; padding: 10px; font-size: 14px;">

<input required name="extra_info[technical_rehearsal]" placeholder="{{ __('estimate.technical_rehearsal') }}" class="extrainfofield" value="" style="width: 100%; margin-top: 10px; border: 0px; background: #fff; border: 1px solid #ece9e9; border-radius: 15px; padding: 10px; font-size: 14px;">

<input required name="extra_info[keynote_from_to]" placeholder="{{ __('estimate.keynote_from_to') }}" class="extrainfofield" value="" style="width: 100%; margin-top: 10px; border: 0px; background: #fff; border: 1px solid #ece9e9; border-radius: 15px; padding: 10px; font-size: 14px;">

<input required name="extra_info[address]" placeholder="{{ __('estimate.address') }}" class="extrainfofield" value="" style="width: 100%; margin-top: 10px; border: 0px; background: #fff; border: 1px solid #ece9e9; border-radius: 15px; padding: 10px; font-size: 14px;">

<input required name="extra_info[parking_info]" placeholder="{{ __('estimate.parking_info') }}" class="extrainfofield" value="" style="width: 100%; margin-top: 10px; border: 0px; background: #fff; border: 1px solid #ece9e9; border-radius: 15px; padding: 10px; font-size: 14px;">

<input required name="extra_info[contact_info_locally]" placeholder="{{ __('estimate.contact_info_locally') }}" class="extrainfofield" value="" style="width: 100%; margin-top: 10px; border: 0px; background: #fff; border: 1px solid #ece9e9; border-radius: 15px; padding: 10px; font-size: 14px;">

<h3 class="text-lg font-semibold mb-2 mt-2" style="font-size: 16px;">
	{{ __('estimate.estimate_extra_info_2') }}
</h3>

<input required name="extra_info[vat_number]" class="extrainfofield" placeholder="{{ __('estimate.vat_number') }}" value="" style="width: 100%; margin-top: 10px; border: 0px; background: #fff; border: 1px solid #ece9e9; border-radius: 15px; padding: 10px; font-size: 14px;">

<input required name="extra_info[invoicing_address]" class="extrainfofield" placeholder="{{ __('estimate.invoicing_address') }}" value="" style="width: 100%; margin-top: 10px; border: 0px; background: #fff; border: 1px solid #ece9e9; border-radius: 15px; padding: 10px; font-size: 14px;">

<input required name="extra_info[invoice_email]" class="extrainfofield" placeholder="{{ __('estimate.invoice_email') }}" value="" style="width: 100%; margin-top: 10px; border: 0px; background: #fff; border: 1px solid #ece9e9; border-radius: 15px; padding: 10px; font-size: 14px;">

<input required name="extra_info[po_ref_number]" class="extrainfofield" placeholder="{{ __('estimate.po_ref_number') }}" value="" style="width: 100%; margin-top: 10px; border: 0px; background: #fff; border: 1px solid #ece9e9; border-radius: 15px; padding: 10px; font-size: 14px;">

<input required name="extra_info[payment_time_end_month]" class="extrainfofield" placeholder="{{ __('estimate.payment_time_end_month') }}" value="" style="width: 100%; margin-top: 10px; border: 0px; background: #fff; border: 1px solid #ece9e9; border-radius: 15px; padding: 10px; font-size: 14px;">

		                            </div>
		                            
		                            <h2 class="text-lg font-semibold mb-2 mt-2">{{ __('estimate.signature') }}</h2>
		                            <div id="signature-pad">
								        <canvas></canvas>
								    </div>
								    <span id="clear" style="display: block; cursor: pointer; width: 60px; text-align: center; font-weight: bold; background: #ff3665; color: #fff; border-radius: 10px; margin-top: 10px; padding: 5px 10px; font-size: 12px;">Clear</span>
								    <input type="hidden" id="signature-input" name="signature">
		                        </section>
							<?php endif; ?>
							
							<?php if($speaker && $booking->extra_info_json): ?>
								<section>
									<br>
		                            <h2 class="text-lg font-semibold mb-2">Callsheet info</h2>
		                            
		                            <?php $extra_info = json_decode($booking->extra_info_json); ?>
		                            
		                            <?php $fields = array(
			                            	'date_prep_meeting',
											'date_keynote',
											'content_keynote',
											'language_keynote',
											'amount_profile_audience',
											'hour_to_arrive',
											'technical_rehearsal',
											'keynote_from_to',
											'address',
											'parking_info',
											'contact_info_locally'
			                            );
			                        ?>
		                            
		                            <?php foreach($fields as $field): ?>
									    <?php if(isset($extra_info->$field)): ?>
									    	<div>
										        <label style="font-weight: bold;"><?= __('estimate.' . $field) ?></label><br>
										        <?= htmlspecialchars($extra_info->$field, ENT_QUOTES, 'UTF-8'); ?>
									    	</div>
									    <?php endif; ?>
									<?php endforeach; ?>
									
									<br>
									<br>
		                            <h2 class="text-lg font-semibold mb-2">Invoicing via</h2>
									<ul>
										<li>Read My Lips BV</li>
										<li>Brasschaatsteenweg 308</li>
										<li>2920 Kalmthout</li>
										<li>&nbsp;</li>
										<li>BTW BE 0831.645.732</li>
										<li>&nbsp;</li>
										<li>Mail to finance@readmylips.be</li>
										<li>Reference: <?=$booking->id;?></li>
									</ul>
								</section>
							<?php endif; ?>
	                    </article>
	                </div>
                
                	<?php if($speaker): ?>
		                <!-- Call to action -->
		                <div class="noprint bottom-0 z-30 w-full lg:w-1/2 bg-white dark:bg-slate-950 !bg-opacity-80 backdrop-blur-sm" style="width: 100%;">
		                    <div class="w-full max-w-xl mx-auto px-4 sm:px-6">
		                        <div class="flex py-4 md:py-6 space-x-4">
			                        <?php if(isset($booking_assets[0])): ?>
				                        <?php if($booking_assets[0]->accepted || $booking_assets[0]->refused): ?>
				                        	<?php if($booking_assets[0]->accepted): ?>
				                        		<p style="text-align: center; width: 100%; color: #65ac9b; font-weight: bold;">{!! __('estimate.booking_accepted') !!}</p>
				                        	<?php else: ?>
				                        		<p style="text-align: center; width: 100%; color: #ff3665; font-weight: bold;">{{ __('estimate.booking_refused') }}</p>
				                        	<?php endif; ?>
				                        <?php else: ?>
				                            <a href="#" class="btn w-full text-white bg-blue-500 hover:bg-blue-600 shadow shadow-black/5 animate-shine bg-[linear-gradient(100deg,theme(colors.blue.500),45%,theme(colors.blue.400),55%,theme(colors.blue.500))] bg-[size:200%_100%] hover:bg-[image:none] refused refuseclick refuse-btn" style="background: #ff3665; width: 40%;">{{ __('estimate.refuse_booking') }}</a>
				                            
				                            <span class="refused" style="width: 60%;">
				                            	<button class="btn w-full text-white bg-blue-500 hover:bg-blue-600 shadow shadow-black/5 animate-shine bg-[linear-gradient(100deg,theme(colors.blue.500),45%,theme(colors.blue.400),55%,theme(colors.blue.500))] bg-[size:200%_100%] hover:bg-[image:none]" name="accept" style="background: #65ac9b;">{{ __('estimate.accept_booking') }}</button>
				                            </span>
				                            <span class="refused" style="display: none; width: 100%;">
				                            	<label style="font-weight: bold;">{{ __('estimate.refuse_reason') }}:</label><br>
				                            	<textarea name="refuse_reason" style="width: 100%; border: 1px solid #ccc; border-radius: 10px;"></textarea>
				                            	<button class="btn w-full text-white bg-blue-500 hover:bg-blue-600 shadow shadow-black/5 animate-shine bg-[linear-gradient(100deg,theme(colors.blue.500),45%,theme(colors.blue.400),55%,theme(colors.blue.500))] bg-[size:200%_100%] hover:bg-[image:none] refusebtn" name="refuse" style="background: #ff3665; width: 100%;" style="display: none;">Refuse booking</button>
				                            </span>
										<?php endif; ?>
									<?php else: ?>
										<p style="text-align: center; width: 100%; color: #ff3665; font-weight: bold;">{{ __('estimate.booking_refused') }}</p>
									<?php endif; ?>
		                        </div>
		                    </div>
		                </div>
					<?php else: ?>
		                <!-- Call to action -->
		                <div class="noprint bottom-0 z-30 w-full lg:w-1/2 bg-white dark:bg-slate-950 !bg-opacity-80 backdrop-blur-sm" style="width: 100%;">
		                    <div class="w-full max-w-xl mx-auto px-4 sm:px-6">
	                            <?php if($booking->customer_accepted || $booking->customer_refused || $booking->status == 80): ?>
		                        	<?php if($booking->customer_accepted): ?>
		                        		<p style="text-align: center; width: 100%; color: #65ac9b; font-weight: bold; margin-bottom: 25px;">{!! __('estimate.estimate_temp_accepted') !!}</p>
		                        	<?php else: ?>
		                        		<?php if($booking->status == 80): ?>
		                        			<p style="text-align: center; width: 100%; color: #3b82f6; font-weight: bold; margin-bottom: 25px;">{{ __('estimate.estimate_call_request') }}</p>
										<?php else: ?>
		                        			<p style="text-align: center; width: 100%; color: #ff3665; font-weight: bold; margin-bottom: 25px;">{{ __('estimate.estimate_refused') }}</p>
										<?php endif; ?>
		                        	<?php endif; ?>
		                        <?php else: ?>
		                        	<?php if($booking->accepted): ?>
		                        		<label for="general" style="display: block; margin-top: 20px; color: #ff3665; font-weight: bold; font-size: 14px;"><input type="checkbox" name="general_conditions" style="margin-right: 10px; border-radius: 3px; width: 20px; height: 20px;" checked><span><a href="https://offer.readmylips.be/customer/rml_voorwaarden_offertes_NL.pdf" target="_blank">{{ __('estimate.accept_general') }}</a></span></label>
		                        	<?php endif; ?>
		                        	
		                        	<div class="flex space-x-4">
			                            <a href="#" class="btn w-full text-white bg-blue-500 hover:bg-blue-600 shadow shadow-black/5 animate-shine bg-[linear-gradient(100deg,theme(colors.blue.500),45%,theme(colors.blue.400),55%,theme(colors.blue.500))] bg-[size:200%_100%] hover:bg-[image:none] refused refuseclick refuse-btn" style="background: #ff3665; width: 40%;">{{ __('estimate.refuse_estimate') }}</a>
			                            
			                            <span class="refused" style="width: 60%;">
			                            	<button class="btn w-full text-white bg-blue-500 hover:bg-blue-600 shadow shadow-black/5 animate-shine bg-[linear-gradient(100deg,theme(colors.blue.500),45%,theme(colors.blue.400),55%,theme(colors.blue.500))] bg-[size:200%_100%] hover:bg-[image:none] hidesig" name="accept" style="background: #65ac9b;">
			                            		<?php if($booking->accepted && $booking->customer_accepted == 0 && $booking->customer_refused == 0): ?>
			                            			{{ __('estimate.accept_estimate') }}
			                            		<?php else: ?>
			                            			{{ __('estimate.accept_estimate_v1') }}*
			                            		<?php endif; ?>
											</button>
			                            </span>
		                        	</div>
		                        	
		                        	<?php if($booking->accepted && $booking->customer_accepted == 0 && $booking->customer_refused == 0): ?>
		                        	<?php else: ?>
			                        	<div class="flex py-4 md:py-6 space-x-4">
				                        	<button class="btn w-full text-white bg-blue-500 hover:bg-blue-600 shadow shadow-black/5 animate-shine bg-[linear-gradient(100deg,theme(colors.blue.500),45%,theme(colors.blue.400),55%,theme(colors.blue.500))] bg-[size:200%_100%] hover:bg-[image:none] hidesig" name="contact">
			                            		{{ __('estimate.request_help') }}
											</button>
			                        	</div>
		                        	<?php endif; ?>
		                        	
			                        <div class="flex">
			                            <?php if($booking->accepted && $booking->customer_accepted == 0 && $booking->customer_refused == 0): ?>
			                            <?php else: ?>
			                            	<small style="display: block; width: 100%; margin-top: 15px; margin-bottom: 15px;">* {{ __('estimate.accept_estimate_v1_start') }}</small>
			                            <?php endif; ?>
			                        </div>
			                        <div class="flex">
			                            <span class="refused" style="display: none; width: 100%;">
			                            	<label style="font-weight: bold;">Reason for refusal:</label><br>
			                            	<textarea name="refuse_reason" style="width: 100%; border: 1px solid #ccc; border-radius: 10px;"></textarea>
			                            	<button class="btn w-full text-white bg-blue-500 hover:bg-blue-600 shadow shadow-black/5 animate-shine bg-[linear-gradient(100deg,theme(colors.blue.500),45%,theme(colors.blue.400),55%,theme(colors.blue.500))] bg-[size:200%_100%] hover:bg-[image:none]" name="refuse" style="background: #ff3665; width: 100%;" style="display: none;">Refuse estimate</button>
			                            </span>
		                        	</div>
								<?php endif; ?>
		                        
		                        <?php if($booking->customer_accepted || $booking->customer_refused): ?>
		                        <?php else: ?>
			                        <div class="flex space-x-4" style="margin-bottom: 30px;">
				                        <a href="mailto:<?=$user_email;?>">{{ __('estimate.get_in_touch') }}</a>
			                        </div>
		                        <?php endif; ?>
		                    </div>
		                </div>
					<?php endif; ?>
	            </form>
            </main>
        </div>
    </div>

    <script src="/assets_estimate/js/vendors/alpinejs.min.js" defer></script>
    <script src="/assets_estimate/js/main.js"></script>
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
	<script>
		$(document).ready(function(){
			$('.refuseclick').click(function(e) {
			    e.preventDefault();
			    
			    $('.refused').toggle();
			    
			    $('.extrainfofield').each(function() {
			        if ($(this).attr('required')) {
			            $(this).removeAttr('required');
			        } else {
			            $(this).attr('required', 'required');
			        }
			    });
			});
			
			document.querySelectorAll('.yes-button').forEach(button => {
		        button.addEventListener('click', function() {
		            // Find the associated checkbox
		            const assetId = this.getAttribute('data-table').split('-')[2];
		            const checkbox = document.getElementById(`checkbox-${assetId}`);
		
		            // Check the checkbox
		            checkbox.checked = true;
		        });
		    });
		
		    document.querySelectorAll('.no-button').forEach(button => {
		        button.addEventListener('click', function() {
		            // Find the associated checkbox
		            const assetId = this.getAttribute('data-table').split('-')[2];
		            const checkbox = document.getElementById(`checkbox-${assetId}`);
		
		            // Uncheck the checkbox
		            checkbox.checked = false;
		        });
		    });
			
		    $('.yes-button').on('click', function(event) {
		        event.preventDefault(); // Prevent page refresh
		        
		        // If Yes is already active, do nothing
		        if ($(this).hasClass('active')) {
		            return;
		        }
		
		        var tableClass = $(this).data('table');
		        var totalElement = $('.total-price');
		        var totalInclElement = $('.total-price-incl');
		        var tableValue = parseFloat($(this).data('value'));
		
		        // Highlight Yes button, dim No button
		        $(this).addClass('active');
		        $(this).siblings('.no-button').removeClass('active');
		
		        // Adjust table opacity and update total
		        $('.' + tableClass).css('opacity', '1');
		
		        // Get current total, convert to float, and adjust it
		        var currentTotal = parseFloat(totalElement.text().replace(/\./g, '').replace(',', '.').replace(/[^\d,\.]/g, ''));
		        currentTotal += tableValue;
		
		        // Format total to European style (4.320,50)
		        var formattedTotal = formatNumber(currentTotal);
		        var formattedTotalIncl = formatNumber(currentTotal * 1.21);
		
		        // Update the totals in the DOM
		        totalElement.text('€ ' + formattedTotal);
		        totalInclElement.text('€ ' + formattedTotalIncl);
		    });
		
		    $('.no-button').on('click', function(event) {
		        event.preventDefault(); // Prevent page refresh
		
		        // If No is already active, do nothing
		        if ($(this).hasClass('active')) {
		            return;
		        }
		
		        var tableClass = $(this).data('table');
		        var totalElement = $('.total-price');
		        var totalInclElement = $('.total-price-incl');
		        var tableValue = parseFloat($(this).data('value'));
		
		        // Highlight No button, dim Yes button
		        $(this).addClass('active');
		        $(this).siblings('.yes-button').removeClass('active');
		
		        // Adjust table opacity and update total
		        $('.' + tableClass).css('opacity', '0.5');
		
		        // Get current total, convert to float, and adjust it
		        var currentTotal = parseFloat(totalElement.text().replace(/\./g, '').replace(',', '.').replace(/[^\d,\.]/g, ''));
		        currentTotal -= tableValue;
		
		        // Format total to European style (4.320,50)
		        var formattedTotal = formatNumber(currentTotal);
		        var formattedTotalIncl = formatNumber(currentTotal * 1.21);
		
		        // Update the totals in the DOM
		        totalElement.text('€ ' + formattedTotal);
		        totalInclElement.text('€ ' + formattedTotalIncl);
		    });
		
		    // Helper function to format numbers to 1.000,00 format
		    function formatNumber(number) {
		        return number.toFixed(2) // Force two decimal places
		            .replace('.', ',') // Replace decimal point with comma
		            .replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // Add dots as thousand separators
		    }
		});
	</script>
	
	<?php if($booking->accepted && $booking->customer_accepted == 0 && $booking->customer_refused == 0): ?>
		<script>
	        document.addEventListener('DOMContentLoaded', (event) => {
	            const canvas = document.querySelector('#signature-pad canvas');
	            const ctx = canvas.getContext('2d');
	            let drawing = false;
	
	            canvas.width = canvas.offsetWidth;
	            canvas.height = canvas.offsetHeight;
	
	            const startDrawing = (e) => {
	                drawing = true;
	                draw(e);
	            };
	
	            const endDrawing = () => {
	                drawing = false;
	                ctx.beginPath();
	                saveSignature();
	                toggleHiddenElements();
	            };
	
	            const draw = (e) => {
	                if (!drawing) return;
	
	                ctx.lineWidth = 2;
	                ctx.lineCap = 'round';
	                ctx.strokeStyle = '#000';
	
	                const rect = canvas.getBoundingClientRect();
	                const x = e.clientX - rect.left;
	                const y = e.clientY - rect.top;
	
	                ctx.lineTo(x, y);
	                ctx.stroke();
	                ctx.beginPath();
	                ctx.moveTo(x, y);
	            };
	
	            const saveSignature = () => {
	                const dataURL = canvas.toDataURL('image/png');
	                document.getElementById('signature-input').value = dataURL;
	            };
	
	            const isCanvasEmpty = () => {
	                const blankCanvas = document.createElement('canvas');
	                blankCanvas.width = canvas.width;
	                blankCanvas.height = canvas.height;
	                return canvas.toDataURL() === blankCanvas.toDataURL();
	            };
	
	            const toggleHiddenElements = () => {
	                const elements = document.querySelectorAll('.hidesig');
	                if (isCanvasEmpty()) {
	                    elements.forEach(element => {
	                        element.disabled = true;
	                        element.style.opacity = '0.5';
	                    });
	                } else {
	                    elements.forEach(element => {
	                        element.disabled = false;
	                        element.style.opacity = '1';
	                    });
	                }
	            };
	
	            canvas.addEventListener('mousedown', startDrawing);
	            canvas.addEventListener('mouseup', endDrawing);
	            canvas.addEventListener('mousemove', draw);
	
	            canvas.addEventListener('touchstart', (e) => startDrawing(e.touches[0]));
	            canvas.addEventListener('touchend', endDrawing);
	            canvas.addEventListener('touchmove', (e) => draw(e.touches[0]));
	
	            document.getElementById('clear').addEventListener('click', () => {
	                ctx.clearRect(0, 0, canvas.width, canvas.height);
	                document.getElementById('signature-input').value = '';
	                toggleHiddenElements();
	            });
	            
	            <?php if($booking->accepted && $booking->customer_accepted == 0 && $booking->customer_refused == 0): ?>
	            	toggleHiddenElements();
	            <?php endif; ?>
	
	            // Initial check to hide elements if canvas is empty
	            toggleHiddenElements();
	        });
	    </script>
	<?php endif; ?>
</body>

</html>