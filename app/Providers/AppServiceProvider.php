<?php

namespace App\Providers;

use App\Repositories\Admin\AddressReference\AddressReferenceRepository;
use App\Repositories\Admin\AddressReference\EloquentAddressReference;
use App\Repositories\Admin\AdminSubscription\AdminUserSubscriptionRepository;
use App\Repositories\Admin\AdminSubscription\EloquentAdminUserSubscription;
use App\Repositories\Admin\Banner\BannerRepository;
use App\Repositories\Admin\Banner\EloquentBanner;
use App\Repositories\Admin\Brand\BrandRepository;
use App\Repositories\Admin\Brand\EloquentBrand;
use App\Repositories\Admin\Category\CategoriesRepository;
use App\Repositories\Admin\Category\EloquentCategories;
use App\Repositories\Admin\Dashboard\DashboardRepository;
use App\Repositories\Admin\Dashboard\EloquentDashboard;
use App\Repositories\Admin\GoogleLocation\EloquentGoogleLocation;
use App\Repositories\Admin\GoogleLocation\GoogleLocationRepository;
use App\Repositories\Admin\Plan\EloquentPlan;
use App\Repositories\Admin\Plan\PlanRepository;
use App\Repositories\Admin\Post\EloquentPost;
use App\Repositories\Admin\Post\PostRepository;
use App\Repositories\Admin\Product\EloquentProduct;
use App\Repositories\Admin\Product\ProductRepository;
use App\Repositories\Admin\Province\EloquentProvince;
use App\Repositories\Admin\Province\ProvinceRepository;
use App\Repositories\Admin\ProvinceCategory\EloquentProvinceCategory;
use App\Repositories\Admin\ProvinceCategory\ProvinceCategoryRepository;
use App\Repositories\Admin\Service\EloquentService;
use App\Repositories\Admin\Service\ServiceRepository;
use App\Repositories\Admin\Slideshow\EloquentSlideShow;
use App\Repositories\Admin\Slideshow\SlideShowRepository;
use App\Repositories\Admin\StoreCategory\EloquentStoreCategory;
use App\Repositories\Admin\StoreCategory\StoreCategoryRepository;
use App\Repositories\Admin\SubCategory\EloquentSubCategory;
use App\Repositories\Admin\SubCategory\SubCategoryRepository;
use App\Repositories\Admin\Subscription\AdminSubscriptionRepository;
use App\Repositories\Admin\Subscription\EloquentAdminSubscription;
use App\Repositories\Admin\User\EloquentUser;
use App\Repositories\Admin\User\UserRepository;
use App\Repositories\Admin\DigitalShowcase\DigitalShowcaseRepository;
use App\Repositories\Admin\DigitalShowcase\EloquentDigitalShowcase;
use App\Repositories\Admin\FoodCategory\FoodCategoryRepository;
use App\Repositories\Admin\FoodCategory\EloquentFoodCategory;
use App\Repositories\Admin\FoodType\FoodTypeRepository;
use App\Repositories\Admin\FoodType\EloquentFoodType;
use App\Repositories\Admin\BankOption\BankOptionRepository;
use App\Repositories\Admin\BankOption\EloquentBankOption;
use App\Repositories\Admin\Promotion\PromotionRepository;
use App\Repositories\Admin\Promotion\EloquentPromotion;
use App\Repositories\Admin\Event\EventRepository;
use App\Repositories\Admin\Event\EloquentEvent;
use App\Repositories\Admin\Guest\GuestRepository;
use App\Repositories\Admin\Guest\EloquentGuest;
use App\Repositories\Front\EloquentFront;
use App\Repositories\Front\FrontRepository;
use App\Repositories\Front\ShowcaseRepository;
use App\Repositories\Front\EloquentShowcase;
use App\Repositories\User\Event\EloquentUserEvent;
use App\Repositories\User\Event\UserEventRepository;
use App\Repositories\User\Guest\EloquentUserGuest;
use App\Repositories\User\Guest\UserGuestRepository;
use App\Repositories\User\Invite\EloquentUserInvite;
use App\Repositories\User\Invite\UserInviteRepository;
use App\Repositories\User\Subscription\EloquentSubscription;
use App\Repositories\User\Subscription\SubscriptionRepository;
use App\Repositories\User\UserDashboard\EloquentUserDashboard;
use App\Repositories\User\UserDashboard\UserDashboardRepository;
use App\Repositories\User\UserSubscription\EloquentUserSubscription;
use App\Repositories\User\UserSubscription\UserSubscriptionRepository;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerWebAdminRepository();
        $this->registerWebUserRepository();
    }

    private function registerWebAdminRepository(): void
    {
        $this->app->singleton(
            DashboardRepository::class,
            EloquentDashboard::class,
        );
        $this->app->singleton(
            PostRepository::class,
            EloquentPost::class,
        );
        $this->app->singleton(
            StoreCategoryRepository::class,
            EloquentStoreCategory::class,
        );
        $this->app->singleton(
            SlideShowRepository::class,
            EloquentSlideShow::class,
        );
        $this->app->singleton(
            BannerRepository::class,
            EloquentBanner::class,
        );
        $this->app->singleton(
            UserRepository::class,
            EloquentUser::class,
        );
        $this->app->singleton(
            ProductRepository::class,
            EloquentProduct::class,
        );
        $this->app->singleton(
            CategoriesRepository::class,
            EloquentCategories::class,
        );
        $this->app->singleton(
            SubCategoryRepository::class,
            EloquentSubCategory::class,
        );
        $this->app->singleton(
            BrandRepository::class,
            EloquentBrand::class,
        );
        $this->app->singleton(
            AddressReferenceRepository::class,
            EloquentAddressReference::class,
        );
        $this->app->singleton(
            ProvinceRepository::class,
            EloquentProvince::class,
        );
        $this->app->singleton(
            ProvinceCategoryRepository::class,
            EloquentProvinceCategory::class,
        );
        $this->app->singleton(
            GoogleLocationRepository::class,
            EloquentGoogleLocation::class,
        );
        $this->app->singleton(
            ServiceRepository::class,
            EloquentService::class,
        );
        $this->app->singleton(
            PlanRepository::class,
            EloquentPlan::class,
        );
        $this->app->singleton(
            AdminSubscriptionRepository::class,
            EloquentAdminSubscription::class,
        );
        $this->app->singleton(
            AdminUserSubscriptionRepository::class,
            EloquentAdminUserSubscription::class,
        );
        $this->app->singleton(
            DigitalShowcaseRepository::class,
            EloquentDigitalShowcase::class,
        );
        $this->app->singleton(
            FoodCategoryRepository::class,
            EloquentFoodCategory::class,
        );
        $this->app->singleton(
            FoodTypeRepository::class,
            EloquentFoodType::class,
        );
        $this->app->singleton(
            BankOptionRepository::class,
            EloquentBankOption::class,
        );
        $this->app->singleton(
            PromotionRepository::class,
            EloquentPromotion::class,
        );
        $this->app->singleton(
            EventRepository::class,
            EloquentEvent::class,
        );
        $this->app->singleton(
            GuestRepository::class,
            EloquentGuest::class,
        );
    }

    private function registerWebUserRepository(): void
    {
        $this->app->singleton(
            FrontRepository::class,
            EloquentFront::class,
        );

        $this->app->singleton(
            UserDashboardRepository::class,
            EloquentUserDashboard::class,
        );
        $this->app->singleton(
            UserEventRepository::class,
            EloquentUserEvent::class,
        );
        $this->app->singleton(
            UserGuestRepository::class,
            EloquentUserGuest::class,
        );
        $this->app->singleton(
            UserInviteRepository::class,
            EloquentUserInvite::class,
        );
        $this->app->singleton(
            UserSubscriptionRepository::class,
            EloquentUserSubscription::class,
        );
        $this->app->singleton(
            SubscriptionRepository::class,
            EloquentSubscription::class,
        );
        $this->app->singleton(
            ShowcaseRepository::class,
            EloquentShowcase::class,
        );
    }

    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
