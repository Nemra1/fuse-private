<?php


require __DIR__ . "../../../config_admin.php";
if (!boomAllow(100)) {
    exit;
}
$store = array();
$store['gold_pack'] = FU_store_market("gold");
$store['rank_pack'] = FU_store_market("rank");
$store['premium'] = FU_store_market("premium");

?>
<style>
th{text-align:inherit}
.table-bordered{border:1px solid #e3e6f0}
.table{width:100%;margin-bottom:1rem;color:#858796}
table{border-radius:5px;overflow:hidden;border-collapse:collapse}
.table-bordered thead td,.table-bordered thead th{border-bottom-width:2px}
.table thead th{vertical-align:bottom;border-bottom:2px solid #e3e6f0}
.table td,.table th{font-size:10px;padding:.5rem .75rem;vertical-align:middle;font-size:14px}
.table td,.table th{padding:.75rem;vertical-align:top;border-top:1px solid #656a7e24}
th{border-top:none!important;border-bottom:none!important}
.pack_image{display:flex;justify-content:center;justify-items:center}
#avatarHolder{width:150px;height:150px;border:2px dashed #ccc;border-radius:50%;background-image:url(default_images/icons/coin_placeholder.png);background-size:cover;background-position:center;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#888;font-size:16px}
#avatar{display:none}
.store_grid-container{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));grid-gap:5px;padding-bottom:15px}
.store_card{width:140px;height:140px;overflow:hidden;background-position:center;background-repeat:no-repeat;background-size:contain;border-radius:15px}
.store_content_gold{background:#b92eff8c;background:-webkit-linear-gradient(to right,#8E54E9,#4776E6);background:linear-gradient(to right,#8e54e98c,#b92effcc)}
.store_content_rank{background:#b92eff8c;background:-webkit-linear-gradient(to right,#8E54E9,#4776E6);background:linear-gradient(to right,#00000021,#fb832e63)}
.store_content{height:100%;width:100%;position:relative;margin:0 auto;border-radius:15px}
.store_logo{position:absolute;right:0;left:0;top:30%}
.store_logo img{width:50px;height:50px;display:flex;justify-content:center;justify-items:center;margin:0 auto;border-radius:50%}
.store_main_text{position:relative;margin:0 auto;text-align:center;display:flex;justify-content:center;justify-items:center;top:4%}
.store_price_text{position:relative;margin:0 auto;text-align:center;display:flex;justify-content:center;justify-items:center;top:55%}
.store_price_text button{padding:4px;border-radius:20px;font-size:smaller;background:#4776E6;background:-webkit-linear-gradient(to right,#8E54E9,#4776E6);background:linear-gradient(to right,#8E54E9,#4776E6);font-weight:700;font-style:italic;font-size:small}
.store_main_text p{padding:5px;background:#000;border-radius:20px;font-size:smaller;color:#fff}
.pack_amount{position:relative;margin:0 auto;text-align:center;display:flex;justify-content:center;justify-items:center;top:49%;color:#fffaf0;font-size:small;font-weight:700}
.store_card{position:relative;cursor:pointer;transition:transform 0.2s,box-shadow .2s;box-shadow:0 0 5px 2px #fb832e8a}
.store_card:hover{transform:scale(1.05)}
.store_card input[type="radio"]{display:none}
.store_card input[type="radio"]:checked + .store_content{border:2px solid #FFD700;box-shadow:0 0 10px rgba(255,215,0,0.5)}
.check-icon{display:none;position:absolute;top:0;right:0;color:#fff;font-size:22px;background:#02bd02;border-radius:50%;width:25px;height:25px;text-align:center}
.store_card input[type="radio"]:checked + .store_content .check-icon{display:block}
.pack-detail{display:flex;flex-direction:column;gap:10px;max-width:600px;margin:auto;padding-top:10px}
.pack-detail-item{display:flex;align-items:center;padding:5px;box-shadow:0 2px 5px rgba(0,0,0,0.1);transition:transform .3s ease;border-radius:10px;border:2px dotted #f7a211}
.pack-detail-item img{width:50px;height:50px;border-radius:50%;margin-right:15px;transition:transform .3s ease}
.pack-detail-info{display:flex;flex-direction:column}
.pack-detail-info .pack-name{font-size:18px;font-weight:700}
.pack-detail-info .p_amount{font-size:14px;margin-top:2px}
/* Container for the wings grid */
.wings-grid-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center; /* Center the grid */
    padding: 20px;
    background-color: #f4f4f4; /* Light background for contrast */
}

/* Individual wing card */
.wing-card {
    background-color: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    width: 330px;
    padding: 8px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

/* Hover effect for wing cards */
.wing-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Heading for the wing card (name of the wings) */
.wing-card h3 {
    font-size: 18px;
    margin-bottom: 10px;
    color: #333333;
}

/* Container for wing images */
.wing-images {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Style for individual wing images */
.wing-images img {
	width: 35px;
	height: 35px;
    height: auto;
    border-radius: 4px;
    transition: transform 0.3s ease;
    text-align: center;
    margin: 0 auto;
}

/* Slight zoom on hover for wing images */
.wing-images img:hover {
    transform: scale(1.1);
}

/* Price of wings in gold */
.wing-price {
    margin-top: 10px;
    font-size: 16px;
    color: #333;
    font-weight: bold;
}


/* Unpaired wings styling (optional) */
.unpaired {
    background-color: #ffeaea;
    border-color: #ff9999;
}

/* Mobile responsiveness */
@media (max-width: 600px) {
    .wings-grid-container {
        flex-direction: column;
        align-items: center;
    }

    .wing-card {
        width: 90%;
    }
}

@media screen and (max-width:468px) {
.store_grid-container{grid-template-columns:repeat(auto-fill,minmax(100px,1fr));grid-gap:6px}
.store_card{width:100px;height:120px;border-radius:10px;overflow:hidden}
.store_logo{top:22%}
.store_main_text{top:1%}
.pack_amount{top:41%}
.store_price_text{top:44%}
}
</style>
<div class="page_indata">
	<div id="page_wrapper">
		<div class="page_full">
				<?php  echo elementTitle('Fuse Marketplace'); ?>
		</div>
		<div class="page_full">
            <div>
				<div class="tab_menu">
					<ul>
						<li class="tab_menu_item tab_selected" data="main_tab" data-z="control_zone">Store Control</li>
						<li class="tab_menu_item" data="main_tab" data-z="main_zone">Gold Package</li>
						<li class="tab_menu_item" data="main_tab" data-z="rank_zone">Rank Package</li>
						<li class="tab_menu_item" data="main_tab" data-z="frames_zone" onclick="getFrames();">Frames Package</li>
						<li class="tab_menu_item" data="main_tab" data-z="wings_zone" onclick="getWings();">Wings Package</li>
						<li class="tab_menu_item" data="main_tab" data-z="premium_zone">Premuim Package</li>
					</ul>
				</div>
			</div>	
			

        <div class="page_element">
        		<div class="btable_auto brelative">
        			<button onclick="add_pack();" class="theme_btn reg_button"><i class="ri-add-circle-line"></i> Add Package</button>
        		</div>
        </div>		
		<div class="page_element">
            <div class="card-body">
				<div id="main_tab">
					<div id="control_zone" class="hide_zone tab_zone" style="display:block;">
						<div class="boom_form">
							<div class="setting_element">
								<p class="label">Enable Store Service</p>
								<select id="set_use_store">
								<?php echo onOff($data['use_store']); ?>
								</select>
							</div>	
							<div class="setting_element">
								<p class="label">Enable Photo Royal Frame</p>
								<small class="error">If you disable the Photo Royal frame, it will hide the (level icons) as well</small>
								<select id="set_use_frame">
								<?php echo onOff($data['use_frame']); ?>
								</select>
							</div>
							<div class="setting_element">
								<p class="label">Enable Wings (Username Wings)</p>
								<select id="set_use_wings">
								<?php echo onOff($data['use_wings']); ?>
								</select>
							</div>	
						</div>
						<button data="store_control" type="button" class="save_admin reg_button theme_btn"><i class="ri-save-line"></i> <?php echo $lang["save"]; ?></button>
					</div>			
					<div id="main_zone" class="tab_zone hide_zone" style="display: none;">
					<div class="table-responsive background_header">
						<div id="pack-form" >
						   
							<table class="table table-bordered" id="pack_list" width="100%" cellspacing="0">
								<thead>
									<tr>
										<th>ID</th>
										<th>ICON</th>
										<th>Package Name</th>
										<th>Amount</th>
										<th>price</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
							 <?php
							 // Check if either is empty
									if (!empty($store['gold_pack'])) {

										foreach ($store['gold_pack'] as $key) { ?>
										<tr id="pack_<?php echo $key['id']?>">
										  <td><?php echo $key['id']?></td>
										  <td><img  src="<?php echo $key['image']?>" class="img-fluid img-thumbnail rounded float-left" alt="<?php echo $key['pack_name']?>"style=" max-width: 50px; "></td>
										  <td><span class="badge badge-info"><?php echo $key['pack_name']?></span></td>
										  <td><i class="fas fa-coins"></i><span class="badge badge-dark"><?php echo $key['p_amounts']?></span></td>
										  <td title="Cost price : pack.price"><span class="badge badge-danger"><i class="ri-exchange-dollar-fill"></i><?php echo $key['price']?></span></td>
										  <td>
											  <button class="reg_button theme_btn" onclick="edit_pack('<?php echo $key['id']?>')">EDIT</button>
										  </td>
										</tr>
										<?php } 
									}
								?>
								

								</tbody>
							</table>
						</div>
					</div>
					</div>
					<div id="rank_zone" class="tab_zone hide_zone" style="display: none;">
					<div class="table-responsive background_header">
						<div id="pack-form" >
						   
							<table class="table table-bordered" id="pack_list" width="100%" cellspacing="0">
								<thead>
									<tr>
										<th>ID</th>
										<th>ICON</th>
										<th>Package Name</th>
										<th>price</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
							 <?php
							 // Check if either is empty
									if (!empty($store['rank_pack'])) {

										foreach ($store['rank_pack'] as $key) { ?>
										<tr id="pack_<?php echo $key['id']?>">
										  <td><?php echo $key['id']?></td>
										  <td><img  src="<?php echo $key['image']?>" class="img-fluid img-thumbnail rounded float-left" alt="<?php echo $key['pack_name']?>"style=" max-width: 50px; "></td>
										  <td><span class="badge badge-info"><?php echo $key['pack_name']?></span></td>
										  <td title="Cost price : pack.price"><span class="badge badge-danger"><i class="ri-exchange-dollar-fill"></i><?php echo $key['price']?></span></td>
										  <td>
											  <button class="reg_button theme_btn" onclick="edit_pack('<?php echo $key['id']?>')">EDIT</button>
										  </td>
										</tr>
										<?php } 
									}
									?>
								

								</tbody>
							</table>
						</div>
					</div>
					</div>
					<div id="frames_zone" class="tab_zone hide_zone" style="display: none;">
					<div class="page_element  d-none">
						<div class="btable_auto brelative">
							<button onclick="resetFrames();" class="delete_btn reg_button"><i class="ri-shield-check-line"></i> Reset Frames Folder</button>
						</div>
					</div>	
					<div class="frames_grid"></div>
					</div> 
					<div id="wings_zone" class="tab_zone hide_zone" style="display: none;">
							<div class="wings_grid"></div>
					</div>  
					<div id="premium_zone" class="tab_zone hide_zone" style="display: none;">
						<div class="table-responsive background_header">
							<div id="pack-form">
								<table class="table table-bordered" id="pack_list" width="100%" cellspacing="0">
									<thead>
										<tr>
											<th>ID</th>
											<th>ICON</th>
											<th>Package Name</th>
											<th>Price</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										<?php 
										// Check if 'premium' store list is not empty
										if (!empty($store['premium'])) {
											foreach ($store['premium'] as $key) { ?>
												<tr id="pack_<?php echo $key['id']; ?>">
													<td><?php echo $key['id']; ?></td>
													<td>
														<img src="<?php echo $key['image']; ?>" class="img-fluid img-thumbnail rounded float-left" 
															 alt="<?php echo $key['pack_name']; ?>" style="max-width: 50px;">
													</td>
													<td><span class="badge badge-info"><?php echo $key['pack_name']; ?></span></td>
													<td title="Cost price: <?php echo $key['price']; ?>">
														<span class="badge badge-danger">
															<i class="ri-exchange-dollar-fill"></i><?php echo $key['price']; ?>
														</span>
													</td>
													<td>
														<button class="reg_button theme_btn" onclick="edit_pack('<?php echo $key['id']; ?>')">EDIT</button>
													</td>
												</tr>
											<?php } 
										} else { ?>
											<tr>
												<td colspan="6" class="text-center">No premium packages available.</td>
											</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
						</div>

					</div>		
				</div>                

            </div>

		</div>
		</div>
	</div>
</div>
<script>
function edit_pack(id){
    $.post(FU_Ajax_Requests_File(), {
            f: "store",
            s: "edit_pack_form",
            pack_id:id,
            token:utk,
        },
        function(response) {
            if(response.status ==200){
                showModal(response.html, 500);
            }
     });
}
function add_pack(){
    $.post(FU_Ajax_Requests_File(), {
            f: "store",
            s: "add_pack_form",
            token:utk,
        },
        function(response) {
            if(response.status ==200){
                showModal(response.html, 500);
                $(".modal_top_empty").html('<b><i class="ri-exchange-dollar-line"></i> Add New Package</b>');
				loadLob('admin/setting_store.php');
            }
     });
}
function deletePack(id){
    $.post(FU_Ajax_Requests_File(), {
            f: "store",
            s: "delete_pack",
			pack_id:id,
            token:utk,
        },
        function(r) {
			if(r.status ==200){
				loadLob('admin/setting_store.php');
				 callSaved(r['message'], 1);
			}else{
				callSaved(r['message'], 3);
			}
     });
}
function getFrames(){
    $.post(FU_Ajax_Requests_File(), {
            f: "store",
            s: "get_frames",
            token:utk,
        },
        function(r) {
			if(r.status ==200){
				$(".frames_grid").html(r.html);
				 //callSaved(r['message'], 1);
			}else{
				callSaved(r['message'], 3);
			}
     });
}
function getWings(){
    $.post(FU_Ajax_Requests_File(), {
            f: "store",
            s: "get_wings",
            token:utk,
        },
        function(r) {
			if(r.status ==200){
				$(".wings_grid").html(r.html);
				 //callSaved(r['message'], 1);
			}else{
				callSaved(r['message'], 3);
			}
     });
}
function resetFrames(){
    $.post(FU_Ajax_Requests_File(), {
            f: "store",
            s: "reset_frames",
            token:utk,
        },
        function(r) {
			if(r.status ==200){
				loadLob('admin/setting_store.php');
				 callSaved(r['message'], 1);
			}else{
				callSaved(r['message'], 3);
			}
     });
}
getFrames();
getWings();
function selectWing(baseName, wing1Url, wing2Url, goldPrice,ext) {
	let selectedWing = {};
    // Store the wing information in an object
    selectedWing = {
        baseName: baseName,
        wing1Url: wing1Url,
        wing2Url: wing2Url,
        goldPrice: goldPrice,
        ext: ext,
    };
    
    // Optionally, store this information in sessionStorage for later use
    sessionStorage.setItem('selectedWing', JSON.stringify(selectedWing));

    console.log("Wing selected:", selectedWing);
}

</script>