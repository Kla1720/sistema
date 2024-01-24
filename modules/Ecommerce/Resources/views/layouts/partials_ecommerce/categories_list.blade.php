<h2 class="carousel-title">CATEGORÍAS</h2>

<div class="home-featured-products owl-carousel owl-theme owl-dots-top">

    @foreach ($categories as $categorie)

    <div class="product">
        <figure class="product-image-container">
            <a  href="/ecommerce/category/{{ $categorie->id }}" class="product-image">
                @if($categorie->image!="imagen-no-disponible.jpg" || $categorie->image !="")
                    <img src="{{ asset('storage/uploads/tags/'.$categorie->image) }}" alt="categoría" class="crop">
                @else
                    <img src="{{ asset('logo/imagen-no-disponible.jpg') }}" class="image" alt="product">
                @endif
            </a>
        </figure>
        <div class="product-details">
            <h2 class="product-title">
                <a href="/ecommerce/category/{{ $categorie->id }}">{{$categorie->name}}</a>
            </h2>
            <h2 class="product-title">
                <a href="/ecommerce/item/{{ $categorie->id }}">{{ $categorie->description }}</a>
            </h2>
        </div><!-- End .product-details -->
    </div><!-- End .product -->

    @endforeach
</div><!-- End .categories proucts -->
