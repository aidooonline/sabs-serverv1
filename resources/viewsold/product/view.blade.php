<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Name')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $product-> name }}</span></dd>


                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Status')}}</span></dt>
                    <dd class="col-sm-8">
                        @if($product->status == 0)
                            <span class="badge badge-success">{{ __(\App\Product::$status[$product->status]) }}</span>
                        @elseif($product->status == 1)
                            <span class="badge badge-danger">{{ __(\App\Product::$status[$product->status]) }}</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Category')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ !empty($product->categorys)?$product->categorys->name:'-'}}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Brand')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ !empty($product->Brands)?$product->Brands->name:'-'}}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Price')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{\Auth::user()->priceFormat($product->price)}}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Tax')}}</span></dt>
                    <dd class="col-sm-8">
                        <span class="text-sm">

                            @foreach($product->tax($product->tax) as $tax)

                                    <div class="tax1">
                                       @if(!empty($tax))
                                       <h6>
                                            <span class="badge badge-primary text-xs small">{{$tax->tax_name .' ('.$tax->rate.' %)'}}</span>
                                        </h6>
                                       @else
                                        <h6>
                                            <span class="badge badge-primary text-xs small">{{ __('No Tax')}}</span>
                                        </h6>
                                        @endif
                                    </div>
                            @endforeach
                        </span>
                    </dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Part Number')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $product->part_number}}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Weight')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $product->weight}}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('URL')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $product->URL}}</span></dd>


                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Description')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $product->description }}</span></dd>

                </dl>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card">
            <div class="card-footer py-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-0">
                        <div class="row align-items-center">
                            <dt class="col-sm-12"><span class="h6 text-sm mb-0">{{__('Assigned User')}}</span></dt>
                            <dd class="col-sm-12"><span class="text-sm">{{ !empty($product->assign_user)?$product->assign_user->name:'-'}}</span></dd>

                            <dt class="col-sm-12"><span class="h6 text-sm mb-0">{{__('Created')}}</span></dt>
                            <dd class="col-sm-12"><span class="text-sm">{{\Auth::user()->dateFormat($product->created_at)}}</span></dd>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="w-100 text-right pr-2">
        @can('Edit Product')
            <a href="{{ route('product.edit',$product->id) }}" class="btn btn-sm btn-secondary btn-icon-only rounded-circle pl-1" data-title="{{__('product Edit')}}"><i class="far fa-edit"></i>
            </a>
        @endcan
    </div>
</div>

