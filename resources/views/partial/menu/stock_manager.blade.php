<li>
    <a href="#" title="Theme Settings" data-filter-tags="theme settings">
        <i class="fal fa-viruses"></i>
        <span class="nav-link-text" data-i18n="nav.theme_settings">Parameter Setup</span>
    </a>
    <ul>
        <li>
           <a href="{{ route('parameter_setup.agent.index') }}" title="Analytics Dashboard" data-filter-tags="application intel analytics dashboard">               
               <span class="nav-link-text" data-i18n="nav.theme_settings_layout_options"><i class="fal fa-dot-circle"></i> &nbsp; Agent User</span>
           </a>
        </li>


        <li>          
           <a href="{{ route('parameter_setup.racks.index') }}" title="Analytics Dashboard" data-filter-tags="application intel analytics dashboard">               
               <span class="nav-link-text" data-i18n="nav.theme_settings_layout_options"><i class="fal fa-dot-circle"></i> &nbsp; Racks</span>
           </a>
        </li>


        <li>          
           <a href="{{ route('parameter_setup.shops.index') }}" title="Analytics Dashboard" data-filter-tags="application intel analytics dashboard">               
               <span class="nav-link-text" data-i18n="nav.theme_settings_layout_options"><i class="fal fa-dot-circle"></i> &nbsp; Shops</span>
           </a>
        </li>

       
    </ul>
</li>


<li>
    <a href="#" title="Theme Settings" data-filter-tags="theme settings">
        <i class="fal fa-stocking"></i>
        <span class="nav-link-text" data-i18n="nav.theme_settings">Stock</span>
    </a>
    <ul>
        <li>
            <a href="{{ route('stock.index') }}" title="Analytics Dashboard" data-filter-tags="application intel analytics dashboard">
               <span class="nav-link-text" data-i18n="nav.theme_settings_layout_options"><i class="fal fa-dot-circle"></i> &nbsp;  Product Stock</span>
           </a>
        </li>


        <li>
            <a href="{{ route('stock.lot_voucher') }}" title="Analytics Dashboard" data-filter-tags="application intel analytics dashboard">
               <span class="nav-link-text" data-i18n="nav.theme_settings_layout_options"><i class="fal fa-dot-circle"></i> &nbsp;  Lot Voucher</span>
           </a>
        </li>

    </ul>
</li>



<li>
  <a href="#" title="Theme Settings" data-filter-tags="theme settings">
    <i class="fal fa-blinds"></i>
      <span class="nav-link-text" data-i18n="nav.theme_settings">Racks Fillup</span>
  </a>
  <ul>
      <li>
         <a href="{{ route('rack.rack-fillup.index') }}" title="Analytics Dashboard" data-filter-tags="application intel analytics dashboard">             
             <span class="nav-link-text" data-i18n="nav.theme_settings_layout_options"><i class="fal fa-dot-circle"></i> &nbsp; Socks Rack Fillup</span>
         </a>
      </li>
      <li>
            <a href="{{ route('rack.mapping.index') }}" title="Analytics Dashboard"
                data-filter-tags="application intel analytics dashboard">
                <span class="nav-link-text" data-i18n="nav.theme_settings_layout_options"><i
                        class="fal fa-dot-circle"></i> &nbsp; Rack Mapping</span>
            </a>
        </li>
        
  </ul>
</li>


<li>
    <a href="{{ route('bill.rack.bill_voucher.voucher_list') }}" title="Analytics Dashboard" data-filter-tags="application intel analytics dashboard">
        <i class="fal fa-file-invoice"></i>
        <span class="nav-link-text" data-i18n="nav.application_intel_analytics_dashboard">Rack Bill Voucher</span>
    </a>
 </li>


<li>
    <a href="#" title="Theme Settings" data-filter-tags="theme settings">
        <i class="fal fa-socks"></i>
        <span class="nav-link-text" data-i18n="nav.theme_settings">Report</span>
    </a>
    <ul>
        <li>
           <a href="{{ route('report.lot.summary') }}" title="Analytics Dashboard" data-filter-tags="application intel analytics dashboard">             
               <span class="nav-link-text" data-i18n="nav.theme_settings_layout_options"><i class="fal fa-dot-circle"></i> &nbsp; Lot Summary</span>
           </a>
        </li>
        <li>
            <a href="{{ route('report.lot_brands') }}" title="Analytics Dashboard" data-filter-tags="application intel analytics dashboard">             
                <span class="nav-link-text" data-i18n="nav.theme_settings_layout_options"><i class="fal fa-dot-circle"></i> &nbsp; Lot Brands</span>
            </a>
        </li>
        <li>
            <a href="{{ route('report.lot.summary') }}" title="Analytics Dashboard" data-filter-tags="application intel analytics dashboard">             
                <span class="nav-link-text" data-i18n="nav.theme_settings_layout_options"><i class="fal fa-dot-circle"></i> &nbsp; Lot Packets</span>
            </a>
        </li>
        <li>
            <a href="{{ route('report.lot.summary') }}" title="Analytics Dashboard" data-filter-tags="application intel analytics dashboard">             
                <span class="nav-link-text" data-i18n="nav.theme_settings_layout_options"><i class="fal fa-dot-circle"></i> &nbsp; Individual Paket Socks</span>
            </a>
        </li>

         <li>
             <a href="" title="Analytics Dashboard" data-filter-tags="application intel analytics dashboard">             
                 <span class="nav-link-text" data-i18n="nav.theme_settings_layout_options"><i class="fal fa-dot-circle"></i> &nbsp; Lot Summary</span>
             </a>
          </li>



        <li>
            <a href="{{ route('report.lot.summary') }}" title="Analytics Dashboard" data-filter-tags="application intel analytics dashboard">             
                <span class="nav-link-text" data-i18n="nav.theme_settings_layout_options"><i class="fal fa-dot-circle"></i> &nbsp; Individual Rack Socks</span>
            </a>
        </li>


        <li>
            <a href="{{ route('report.socks_code_generate') }}" title="Analytics Dashboard" data-filter-tags="application intel analytics dashboard">             
                <span class="nav-link-text" data-i18n="nav.theme_settings_layout_options"><i class="fal fa-dot-circle"></i> &nbsp; Socks Code Generate</span>
            </a>
        </li>
        
        <li>
            <a href="{{ route('report.packet_code_generate') }}" title="Analytics Dashboard" data-filter-tags="application intel analytics dashboard">             
                <span class="nav-link-text" data-i18n="nav.theme_settings_layout_options"><i class="fal fa-dot-circle"></i> &nbsp; Packet Code Generate</span>
            </a>
        </li>


        <li>
            <a href="{{ route('report.product') }}" title="Analytics Dashboard" data-filter-tags="application intel analytics dashboard">             
                <span class="nav-link-text" data-i18n="nav.theme_settings_layout_options"><i class="fal fa-dot-circle"></i> &nbsp; Product Report</span>
            </a>
        </li>
        
          <li>
            <a href="{{ route('report.rack_product') }}" title="Analytics Dashboard" data-filter-tags="application intel analytics dashboard">             
                <span class="nav-link-text" data-i18n="nav.theme_settings_layout_options"><i class="fal fa-dot-circle"></i> &nbsp; Rack Product</span>
            </a>
        </li>

         <li>
            <a href="{{ route('report.stock.summary') }}" title="Analytics Dashboard"
                data-filter-tags="application intel analytics dashboard">
                <span class="nav-link-text" data-i18n="nav.theme_settings_layout_options"><i
                        class="fal fa-dot-circle"></i> &nbsp; Stock Summary </span>
            </a>
        </li>


        <li>
            <a href="{{ route('report.shop.voucher.index') }}" title="Analytics Dashboard"
                data-filter-tags="application intel analytics dashboard">
                <span class="nav-link-text" data-i18n="nav.theme_settings_layout_options"><i
                        class="fal fa-dot-circle"></i> &nbsp; Shop Voucher  </span>
            </a>
        </li>



    </ul>
  </li>

  