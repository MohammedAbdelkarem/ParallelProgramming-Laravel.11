<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * Class ProductService.
 */
class ProductService
{
    /**
     * جلب المنتجات مع كاش لأول 3 صفحات فقط
     */
    public function index($data)
    {
        return getOrPaginate(Product::query()->orderByDesc("created_at"), $data);
        // $page = (int) ($data['page'] ?? 1);
        // $perPage = (int) ($data['per_page'] ?? 15);

        // if ($page > 3) {
        //     return getOrPaginate(Product::query()->orderByDesc("created_at"), $data);
        // }

        // $cacheKey = "products:index:page:{$page}:limit:{$perPage}";

        // // جلب البيانات إذا كانت موجودة في الكاش
        // if (Cache::has($cacheKey)) {
        //     return Cache::get($cacheKey);
        // }

        // // إذا لم تكن موجودة، نضع قفل موزع مخصص لبناء كاش هذه الصفحة
        // // هذا يضمن أن مستخدماً واحداً فقط سيقوم بعمل Query في قاعدة البيانات
        // $lock = Cache::lock("lock:rebuild:cache:page:{$page}", 5);

        // return $lock->block(5, function () use ($cacheKey, $data) {
        //     // نتحقق مجدداً في حال قام طلب آخر ببناء الكاش أثناء فترة الانتظار
        //     if (Cache::has($cacheKey)) {
        //         return Cache::get($cacheKey);
        //     }

        //     $result = getOrPaginate(Product::query()->orderByDesc("created_at"), $data);
            
        //     Cache::put($cacheKey, $result, now()->addHours(2));

        //     return $result;
        // });
    }

    /**
     * إضافة منتج جديد وتدمير كاش أول 3 صفحات
     */
    public function store($data)
    {
        $product = Product::create($data);

        // كسر كاش الصفحات الثلاث فوراً
        $this->clearFirstThreePagesCache($data['per_page'] ?? 15);

        return $product;
    }

    /**
     * تحديث منتج وتدمير كاش أول 3 صفحات
     */
    public function update($id, $data)
    {
        return DB::transaction(function () use ($id, $data) {

            // Lock the row for update
            $product = Product::where('id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            $product->update($data);

            // كسر كاش الصفحات الثلاث لأن البيانات المحدثة قد تكون في إحداها
            $this->clearFirstThreePagesCache($data['per_page'] ?? 15);

            return $product;
        });
    }

    /**
     * دالة مساعدة لحذف كاش أول 3 صفحات مباشرة وبكفاءة عالية
     */
    private function clearFirstThreePagesCache($perPage)
    {
        // حذف الكاش للصفحات 1 و 2 و 3 مباشرة دون الحاجة لـ Tags أو مصفوفات تتبع
        Cache::forget("products:index:page:1:limit:{$perPage}");
        Cache::forget("products:index:page:2:limit:{$perPage}");
        Cache::forget("products:index:page:3:limit:{$perPage}");
    }

}
