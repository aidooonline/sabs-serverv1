<?php
    $logo=asset(Storage::url('uploads/logo/'));
    $company_logo=Utility::getValByName('company_logo');
    $users=\Auth::user();
    $currantLang = $users->currentLanguage();
?>
<div class="sidenav custom-sidenav" style="padding-bottom:50px;" id="sidenav-main">

    <!-- Sidenav header -->
    <div class="sidenav-header d-flex align-items-center">
        <a style="color:#ffffff !important;font-weight:bolder !important;padding-left:50px !important;font-size:30px !important;" href="<?php echo e(route('dashboard')); ?>">
            eflo<span style="color:gold;">Q</span>
           <!-- <img class="img-fluid" src="<?php echo e($logo.'/'.(isset($company_logo) && !empty($company_logo)?$company_logo:'logo.png')); ?>" alt="">-->
        </a>
        <div class="ml-auto">
            <!-- Sidenav toggler -->
            <div class="sidenav-toggler sidenav-toggler-dark d-md-none" data-action="sidenav-unpin" data-target="#sidenav-main">
                <div class="sidenav-toggler-inner">
                    <i class="sidenav-toggler-line bg-white"></i>
                    <i class="sidenav-toggler-line bg-white"></i>
                    <i class="sidenav-toggler-line bg-white"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="scrollbar-inner">
        <div class="div-mega">
            <ul class="navbar-nav navbar-nav-docs">
                <li class="nav-item">
                    <a href="<?php echo e(route('dashboard')); ?>" class="nav-link <?php echo e((Request::segment(1) == 'dashboard' || Request::segment(1) == '')?'active':''); ?>">
                        <i class="fas fa-home"></i><?php echo e(__('Home')); ?>

                    </a>
                </li>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Role')): ?>
                    <li class="nav-item">
                        <a href="<?php echo e(route('role.index')); ?>" class="nav-link <?php echo e((Request::segment(1) == 'role')?'active':''); ?>">
                            <i class="fas fa-user-tag"></i><?php echo e(__('Role')); ?>

                        </a>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage User')): ?>
                    <li class="nav-item">
                        <a href="<?php echo e(!empty(\Auth::user()->getDefualtViewRouteByModule('user'))?route(\Auth::user()->getDefualtViewRouteByModule('user')):route('user.index')); ?>" class="nav-link <?php echo e((Request::segment(1) == 'user')?'active':''); ?>">
                            <i class="fas fa-user-circle"></i><?php echo e(__('Users')); ?>

                        </a>
                    </li>
                <?php endif; ?>
                <?php if(\Auth::user()->type=='super admin'): ?>
                    <li class="nav-item">
                        <a href="<?php echo e(route('coupon.index')); ?>" class="nav-link <?php echo e((Request::segment(1) == 'coupon')?'active':''); ?>">
                            <i class="fas fa-briefcase"></i><?php echo e(__('Coupon')); ?>

                        </a>
                    </li>
                <?php endif; ?>
                <?php if(\Auth::user()->type !='super admin'): ?>
                    <li class="nav-item" style="display:none;">
                        <a href="<?php echo e(url('messages')); ?>" class="nav-link <?php echo e((Request::segment(1) == 'messages')?'active':''); ?>">
                            <i class="fab fa-facebook-messenger"></i><?php echo e(__('Messenger')); ?>

                        </a>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Form Builder')): ?>
                    <li class="nav-item " style="display:none;">
                        <a href="<?php echo e(route('form_builder.index')); ?>" class="nav-link <?php echo e((Request::segment(1) == 'form_builder' || Request::segment(1) == 'form_response')?'active open':''); ?>">
                            <i class="fas fa-align-justify"></i> <?php echo e(__('Form Builder')); ?>

                        </a>
                    </li>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Lead')): ?>
                <li class="nav-item">
                    <a href="<?php echo e(!empty(\Auth::user()->getDefualtViewRouteByModule('lead'))?route(\Auth::user()->getDefualtViewRouteByModule('lead')):route('lead.index')); ?>" class="nav-link <?php echo e((Request::segment(1) == 'lead')?'active':''); ?>">
                        <i class="fas fa-address-card"></i><?php echo e(__('Leads')); ?>

                    </a>
                </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Contact')): ?>
            <li class="nav-item" style="display:none;">
                <a href="<?php echo e(!empty(\Auth::user()->getDefualtViewRouteByModule('contact'))?route(\Auth::user()->getDefualtViewRouteByModule('contact')):route('contact.index')); ?>" class="nav-link <?php echo e((Request::segment(1) == 'contact')?'active':''); ?>">
                    <i class="fas fa-id-badge"></i><?php echo e(__('Contacts')); ?>

                </a>
            </li>
        <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Account')): ?>
                    <li class="nav-item">
                        <a href="<?php echo e(!empty(\Auth::user()->getDefualtViewRouteByModule('account'))?route(\Auth::user()->getDefualtViewRouteByModule('account')):route('account.index')); ?>" class="nav-link <?php echo e((Request::segment(1) == 'account')?'active':''); ?>">
                            <i class="fas fa-building"></i><?php echo e(__('Accounts')); ?>

                        </a>
                    </li>
                <?php endif; ?>

               

              
                
                
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Opportunities')): ?>
                    <li class="nav-item">
                        <a href="<?php echo e(!empty(\Auth::user()->getDefualtViewRouteByModule('opportunities'))?route(\Auth::user()->getDefualtViewRouteByModule('opportunities')):route('opportunities.index')); ?>" class="nav-link <?php echo e((Request::segment(1) == 'opportunities')?'active':''); ?>">
                            <i class="fas fa-dollar-sign"></i><?php echo e(__('Deals')); ?>

                        </a>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Product')): ?>
                    <li class="nav-item">
                        <a href="<?php echo e(!empty(\Auth::user()->getDefualtViewRouteByModule('product'))?route(\Auth::user()->getDefualtViewRouteByModule('product')):route('product.index')); ?>" class="nav-link <?php echo e((Request::segment(1) == 'product')?'active':''); ?>">
                            <i class="fas fa-cube"></i><?php echo e(__('Properties')); ?>

                        </a>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Quote')): ?>
                    <li class="nav-item" style="display:none;">
                        <a href="<?php echo e(route('quote.index')); ?>" class="nav-link <?php echo e((Request::segment(1) == 'quote')?'active':''); ?>">
                            <i class="fas fa-file-invoice-dollar"></i><?php echo e(__('Quotes')); ?>

                        </a>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage SalesOrder')): ?>
                    <li class="nav-item" style="display:none;">
                        <a href="<?php echo e(route('salesorder.index')); ?>" class="nav-link <?php echo e((Request::segment(1) == 'salesorder')?'active':''); ?>">
                            <i class="fas fa-file-invoice"></i><?php echo e(__('Sales Orders')); ?>

                        </a>
                    </li>
                 <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Invoice')): ?>
                    <li class="nav-item">
                        <a href="<?php echo e(route('invoice.index')); ?>" class="nav-link <?php echo e((Request::segment(1) == 'invoice')?'active':''); ?>">
                            <i class="fas fa-receipt"></i><?php echo e(__('Invoices')); ?>

                        </a>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage CommonCase')): ?>
                    <li class="nav-item" style="display:none;">
                        <a href="<?php echo e(!empty(\Auth::user()->getDefualtViewRouteByModule('commoncases'))?route(\Auth::user()->getDefualtViewRouteByModule('commoncases')):route('commoncases.index')); ?>" class="nav-link <?php echo e((Request::segment(1) == 'commoncases'||Request::segment(1) == 'commoncase')?'active':''); ?>">
                            <i class="fas fa-briefcase"></i><?php echo e(__('Cases')); ?>

                        </a>
                    </li>
                <?php endif; ?>
                <?php if(\Auth::user()->type!='super admin'): ?>
                    <li class="nav-item">
                        <a href="<?php echo e(route('calendar.index')); ?>" class="nav-link <?php echo e((Request::segment(1) == 'calendar')?'active':''); ?>">
                            <i class="far fa-calendar-alt"></i><?php echo e(__('Calendar')); ?>

                        </a>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Task')): ?>
                    <li class="nav-item">
                        <a href="<?php echo e(!empty(\Auth::user()->getDefualtViewRouteByModule('task'))?route(\Auth::user()->getDefualtViewRouteByModule('task')):route('task.index')); ?>" class="nav-link <?php echo e((Request::segment(1) == 'task')?'active':''); ?>">
                            <i class="fas fa-tasks"></i><?php echo e(__('Task')); ?>

                        </a>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Meeting')): ?>
                    <li class="nav-item">
                        <a href="<?php echo e(!empty(\Auth::user()->getDefualtViewRouteByModule('meeting'))?route(\Auth::user()->getDefualtViewRouteByModule('meeting')):route('meeting.index')); ?>" class="nav-link <?php echo e((Request::segment(1) == 'meeting')?'active':''); ?>">
                            <i class="fas fa-calendar-check"></i><?php echo e(__('Meeting')); ?>

                        </a>
                    </li>
                <?php endif; ?>
                
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Document')): ?>
                    <li class="nav-item">
                        <a href="<?php echo e(!empty(\Auth::user()->getDefualtViewRouteByModule('document'))?route(\Auth::user()->getDefualtViewRouteByModule('document')):route('document.index')); ?>" class="nav-link <?php echo e((Request::segment(1) == 'document')?'active':''); ?>">
                            <i class="fas fa-file-alt"></i><?php echo e(__('Document')); ?></a>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Campaign')): ?>
                    <li class="nav-item">
                        <a href="<?php echo e(!empty(\Auth::user()->getDefualtViewRouteByModule('campaign'))?route(\Auth::user()->getDefualtViewRouteByModule('campaign')):route('campaign.index')); ?>" class="nav-link <?php echo e((Request::segment(1) == 'campaign')?'active':''); ?>">
                            <i class="fas fa-chart-line"></i><?php echo e(__('Campaigns')); ?></a>
                    </li>
                <?php endif; ?>
                
                <?php if(\Auth::user()->type=='super admin' || \Auth::user()->type=='owner'): ?>
                    <li class="nav-item">
                        <a href="<?php echo e(route('plan.index')); ?>" class="nav-link <?php echo e((Request::segment(1) == 'plan')?'active':''); ?>">
                            <i class="fas fa-trophy"></i><?php echo e(__('Plan')); ?>

                        </a>
                    </li>
                <?php endif; ?>
                <?php if(\Auth::user()->type=='super admin' || \Auth::user()->type=='owner'): ?>
                    <li class="nav-item">
                        <a href="<?php echo e(route('order.index')); ?>" class="nav-link <?php echo e((Request::segment(1) == 'order')?'active':''); ?>">
                            <i class="fas fa-cart-plus"></i><?php echo e(__('Order')); ?>

                        </a>
                    </li>
                <?php endif; ?>
                <?php if(Gate::check('Manage Report')): ?>
                    <li class="nav-item ">
                        <a class="nav-link collapsed <?php echo e((Request::segment(1) == 'report' || Request::segment(2) == 'leadsanalytic' || Request::segment(2) == 'invoiceanalytic' || Request::segment(2) == 'quoteanalytic')?'true':'false'); ?>" href="#navbar-getting-starte1" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="navbar-getting-started">
                            <i class="fas fa-chart-bar"></i><?php echo e(__('Reports')); ?>

                            <i class="fas fa-sort-up"></i>
                        </a>
                        <div class="collapse <?php echo e((Request::segment(1) == 'report' || Request::segment(2) == 'leadsanalytic' || Request::segment(2) == 'invoiceanalytic' || Request::segment(2) == 'quoteanalytic')?'show':''); ?>" id="navbar-getting-starte1" style="">
                            <ul class="nav flex-column">
                                <li class="nav-item <?php echo e((Request::segment(1) == 'report' && Request::segment(2) == '')?'active open':''); ?>">
                                    <a href="<?php echo e(route('report.index')); ?>" class="nav-link">
                                        <?php echo e(__('Custom Report')); ?></a>
                                </li>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Report')): ?>
                                    <li class="nav-item <?php echo e((Request::segment(2) == 'leadsanalytic')?'active open':''); ?>">
                                        <a href="<?php echo e(route('report.leadsanalytic')); ?>" class="nav-link"><?php echo e(__('Leads Analytics')); ?></a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Report')): ?>
                                    <li class="nav-item <?php echo e((Request::segment(2) == 'invoiceanalytic')?'active open':''); ?>">
                                        <a href="<?php echo e(route('report.invoiceanalytic')); ?>" class="nav-link"><?php echo e(__('Invoice Analytics')); ?></a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Report')): ?>
                                    <li class="nav-item <?php echo e((Request::segment(2) == 'salesorderanalytic')?'active open':''); ?>">
                                        <a href="<?php echo e(route('report.salesorderanalytic')); ?>" class="nav-link"><?php echo e(__('Sales Order Analytics')); ?></a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Report')): ?>
                                    <li class="nav-item <?php echo e((Request::segment(2) == 'quoteanalytic')?'active open':''); ?>">
                                        <a href="<?php echo e(route('report.quoteanalytic')); ?>" class="nav-link"><?php echo e(__('Quote Analytics')); ?></a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>
                <?php if(Gate::check('Manage AccountType') || Gate::check('Manage AccountIndustry') || Gate::check('Manage LeadSource') || Gate::check('Manage OpportunitiesStage') || Gate::check('Manage CaseType') || Gate::check('Manage DocumentType') || Gate::check('Manage DocumentFolder') || Gate::check('Manage TargetList')|| Gate::check('Manage CampaignType') || Gate::check('Manage ProductCategory') || Gate::check('Manage ProductBrand')||Gate::check('Manage ProductTax') || Gate::check('Manage ShippingProvider')|| Gate::check('Manage TaskStage')): ?>
                    <li class="nav-item">
                        <a class="nav-link collapsed <?php echo e((Request::segment(1) == 'account_type' || Request::segment(1) == 'account_industry' || Request::segment(1) == 'lead_source' || Request::segment(1) == 'opportunities_stage' || Request::segment(1) == 'case_type' || Request::segment(1) == 'document_folder' || Request::segment(1) == 'document_type' || Request::segment(1) == 'target_list' || Request::segment(1) == 'campaign_type' || Request::segment(1) == 'product_category' || Request::segment(1) == 'product_brand' || Request::segment(1) == 'product_tax' || Request::segment(1) == 'shipping_provider' || Request::segment(1) == 'task_stage')?'true':'false'); ?>"
                           href="#navbar-getting-started" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="navbar-getting-started">
                            <i class="fas fa-cogs"></i><?php echo e(__('Categories')); ?>

                            <i class="fas fa-sort-up"></i>
                        </a>
                        <div
                            class="collapse <?php echo e((Request::segment(1) == 'account_type' || Request::segment(1) == 'account_industry' || Request::segment(1) == 'lead_source' || Request::segment(1) == 'opportunities_stage' || Request::segment(1) == 'case_type' || Request::segment(1) == 'document_folder' || Request::segment(1) == 'document_type' || Request::segment(1) == 'target_list' || Request::segment(1) == 'campaign_type' || Request::segment(1) == 'product_category' || Request::segment(1) == 'product_brand' || Request::segment(1) == 'product_tax' || Request::segment(1) == 'shipping_provider' || Request::segment(1) == 'task_stage')?'show':''); ?>"
                            id="navbar-getting-started" style="">
                            <ul class="nav flex-column">
                                <?php if(Gate::check('Manage AccountType') || Gate::check('Manage AccountIndustry')): ?>
                                    <li class="nav-item submenu-li">
                                        <a class="nav-link collapsed" href="#navbar-getting-started1" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="navbar-getting-started1">
                                            <?php echo e(__('Account')); ?>

                                            <i class="fas fa-sort-up"></i>
                                        </a>
                                        <div class="collapse submenu-ul <?php echo e((Request::segment(1) == 'account_type' || Request::segment(1) == 'account_industry')?'show':''); ?>" id="navbar-getting-started1" style="">
                                            <ul class="nav flex-column">
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage AccountType')): ?>
                                                    <li class="nav-item <?php echo e((Request::segment(1) == 'account_type')?'active open':''); ?>">
                                                        <a href="<?php echo e(route('account_type.index')); ?>" class="nav-link"><?php echo e(__('Type')); ?></a>
                                                    </li>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage AccountIndustry')): ?>
                                                    <li class="nav-item <?php echo e((Request::segment(1) == 'account_industry')?'active open':''); ?>">
                                                        <a href="<?php echo e(route('account_industry.index')); ?>" class="nav-link"><?php echo e(__('Industry')); ?></a>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </li>
                                <?php endif; ?>
                                <?php if(Gate::check('Manage DocumentType')||Gate::check('Manage DocumentFolder')): ?>
                                    <li class="nav-item submenu-li">
                                        <a class="nav-link collapsed" href="#navbar-getting-started5" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="navbar-getting-started1">
                                            <?php echo e(__('Document')); ?>

                                            <i class="fas fa-sort-up"></i>
                                        </a>
                                        <div class="collapse submenu-ul <?php echo e((Request::segment(1) == 'document_folder' || Request::segment(1) == 'document_type')?'show':''); ?>" id="navbar-getting-started5" style="">
                                            <ul class="nav flex-column">
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage DocumentFolder')): ?>
                                                    <li class="nav-item <?php echo e((Request::segment(1) == 'document_folder')?'active open':''); ?>">
                                                        <a href="<?php echo e(route('document_folder.index')); ?>" class="nav-link"><?php echo e(__('Folder')); ?></a>
                                                    </li>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage DocumentType')): ?>
                                                    <li class="nav-item <?php echo e((Request::segment(1) == 'document_type')?'active open':''); ?>">
                                                        <a href="<?php echo e(route('document_type.index')); ?>" class="nav-link"><?php echo e(__('Type')); ?></a>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </li>
                                <?php endif; ?>
                                <?php if(Gate::check('Manage TargetList')||Gate::check('Manage CampaignType')): ?>
                                    <li class="nav-item submenu-li">
                                        <a class="nav-link collapsed" href="#navbar-getting-started6" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="navbar-getting-started1">
                                            <?php echo e(__('Campaign')); ?>

                                            <i class="fas fa-sort-up"></i>
                                        </a>
                                        <div class="collapse submenu-ul <?php echo e((Request::segment(1) == 'target_list' || Request::segment(1) == 'campaign_type')?'show':''); ?>" id="navbar-getting-started6" style="">
                                            <ul class="nav flex-column">
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage TargetList')): ?>
                                                    <li class="nav-item <?php echo e((Request::segment(1) == 'target_list')?'active open':''); ?>">
                                                        <a href="<?php echo e(route('target_list.index')); ?>" class="nav-link"><?php echo e(__('Target Lists')); ?></a>
                                                    </li>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage CampaignType')): ?>
                                                    <li class="nav-item <?php echo e((Request::segment(1) == 'campaign_type')?'active open':''); ?>">
                                                        <a href="<?php echo e(route('campaign_type.index')); ?>" class="nav-link"><?php echo e(__('Type')); ?></a>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </li>
                                <?php endif; ?>
                                <?php if(Gate::check('Manage ProductCategory')||Gate::check('Manage ProductBrand')||Gate::check('Manage ProductTax')): ?>
                                    <li class="nav-item submenu-li">
                                        <a class="nav-link collapsed" href="#navbar-getting-started7" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="navbar-getting-started1">
                                            <?php echo e(__('Property')); ?>

                                            <i class="fas fa-sort-up"></i>
                                        </a>
                                        <div class="collapse submenu-ul <?php echo e((Request::segment(1) == 'product_category' || Request::segment(1) == 'product_brand' || Request::segment(1) == 'product_tax')?'show':''); ?>" id="navbar-getting-started7" style="">
                                            <ul class="nav flex-column">
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage ProductCategory')): ?>
                                                    <li class="nav-item <?php echo e((Request::segment(1) == 'product_category')?'active open':''); ?>">
                                                        <a href="<?php echo e(route('product_category.index')); ?>" class="nav-link"><?php echo e(__('Category')); ?></a>
                                                    </li>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage ProductBrand')): ?>
                                                    <li class="nav-item <?php echo e((Request::segment(1) == 'product_brand')?'active open':''); ?>">
                                                        <a href="<?php echo e(route('product_brand.index')); ?>" class="nav-link"><?php echo e(__('Brand')); ?></a>
                                                    </li>
                                                <?php endif; ?>
                                                
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage ProductTax')): ?>
                                                    <li class="nav-item <?php echo e((Request::segment(1) == 'product_tax')?'active open':''); ?>">
                                                        <a href="<?php echo e(route('product_tax.index')); ?>" class="nav-link"><?php echo e(__('Tax')); ?></a>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </li>
                                <?php endif; ?>
                               <?php if(Gate::check('Manage LeadSource')): ?>
                                    <li class="nav-item <?php echo e((Request::segment(1) == 'lead_source')?'active open':''); ?>">
                                        <a href="<?php echo e(route('lead_source.index')); ?>" class="nav-link"><?php echo e(__('Lead Source')); ?></a>
                                    </li>
                                <?php endif; ?> 
                                 
                                <?php if(Gate::check('Manage OpportunitiesStage')): ?>
                                    <li class="nav-item <?php echo e((Request::segment(1) == 'opportunities_stage')?'active open':''); ?>">
                                        <a href="<?php echo e(route('opportunities_stage.index')); ?>" class="nav-link"><?php echo e(__('Deal Stage')); ?></a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if(Gate::check('Manage TaskStage')): ?>
                                    <li class="nav-item <?php echo e((Request::segment(1) == 'task_stage')?'active open':''); ?>">
                                        <a href="<?php echo e(route('task_stage.index')); ?>" class="nav-link"><?php echo e(__('Task Stage')); ?></a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>
                <?php if(\Auth::user()->type=='super admin'): ?>
                    <li class="nav-item">
                        <a href="<?php echo e(route('settings')); ?>" class="nav-link <?php echo e((Request::segment(1) == 'settings')?'active':''); ?>">
                            <i class="fas fa-cog"></i><?php echo e(__('Settings')); ?>

                        </a>
                    </li>
                <?php endif; ?>

            </ul>
        </div>
    </div>
</div>
<?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/partials/admin/menu.blade.php ENDPATH**/ ?>