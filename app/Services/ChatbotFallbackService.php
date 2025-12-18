<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ChatbotFallbackService
{
    /**
     * Xử lý câu hỏi bằng rule-based system khi AI không thể trả lời
     */
    public function processQuestion(string $question): string
    {
        $question = mb_strtolower(trim($question), 'UTF-8');
        
        // 1. Chào hỏi
        if ($this->isGreeting($question)) {
            return "Xin chào! Tôi là trợ lý ảo của shop 46 Perfume. Tôi có thể giúp bạn tìm hiểu về các sản phẩm nước hoa, mùi hương, giá cả và đưa ra gợi ý phù hợp. Bạn cần tư vấn gì ạ?";
        }
        
        // 2. Hỏi về ngân sách/giá (kiểm tra trước để bắt được "sản phẩm giá X")
        if ($this->isBudgetQuestion($question) || $this->isPriceQuestion($question)) {
            // Nếu có từ "giá" và số, ưu tiên xử lý như câu hỏi về giá cụ thể
            if (strpos($question, 'giá') !== false && preg_match('/\d+/', $question)) {
                return $this->handlePriceQuestion($question);
            }
            // Nếu có từ "dưới", "khoảng" thì dùng handleBudgetQuestion
            if (strpos($question, 'dưới') !== false || strpos($question, 'khoảng') !== false || strpos($question, 'tầm') !== false) {
                return $this->handleBudgetQuestion($question);
            }
            // Ngược lại dùng handlePriceQuestion
            return $this->handlePriceQuestion($question);
        }
        
        // 3. Hỏi về mùi hương
        if ($this->isScentQuestion($question)) {
            return $this->handleScentQuestion($question);
        }
        
        // 4. Hỏi về thương hiệu
        if ($this->isBrandQuestion($question)) {
            return $this->handleBrandQuestion($question);
        }
        
        // 5. Hỏi về sản phẩm theo giới tính
        if ($this->isGenderQuestion($question)) {
            return $this->handleGenderQuestion($question);
        }
        
        // 6. Hỏi về sản phẩm bán chạy
        if ($this->isBestSellerQuestion($question)) {
            return $this->handleBestSellerQuestion();
        }
        
        // 8. Tìm kiếm sản phẩm theo từ khóa
        $productResults = $this->searchProducts($question);
        if (!empty($productResults)) {
            return $productResults;
        }
        
        // 9. Câu trả lời mặc định
        return $this->getDefaultResponse();
    }
    
    /**
     * Kiểm tra có phải câu chào hỏi không
     */
    private function isGreeting(string $question): bool
    {
        $greetings = ['xin chào', 'chào', 'hello', 'hi', 'chào bạn', 'hey'];
        foreach ($greetings as $greeting) {
            if (strpos($question, $greeting) !== false) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Kiểm tra có phải câu hỏi về giá không
     */
    private function isPriceQuestion(string $question): bool
    {
        $priceKeywords = ['giá', 'bao nhiêu', 'cost', 'price', 'đắt', 'rẻ', 'chi phí'];
        foreach ($priceKeywords as $keyword) {
            if (strpos($question, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Kiểm tra có phải câu hỏi về mùi hương không
     */
    private function isScentQuestion(string $question): bool
    {
        $scentKeywords = ['mùi', 'hương', 'scent', 'fragrance', 'thơm', 'mùi hương', 'vị'];
        foreach ($scentKeywords as $keyword) {
            if (strpos($question, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Kiểm tra có phải câu hỏi về thương hiệu không
     */
    private function isBrandQuestion(string $question): bool
    {
        $brandKeywords = ['thương hiệu', 'brand', 'hãng', 'nhãn hiệu'];
        foreach ($brandKeywords as $keyword) {
            if (strpos($question, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Kiểm tra có phải câu hỏi về giới tính không
     */
    private function isGenderQuestion(string $question): bool
    {
        $genderKeywords = ['nam', 'nữ', 'male', 'female', 'đàn ông', 'phụ nữ', 'nam giới', 'nữ giới'];
        foreach ($genderKeywords as $keyword) {
            if (strpos($question, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Kiểm tra có phải câu hỏi về sản phẩm bán chạy không
     */
    private function isBestSellerQuestion(string $question): bool
    {
        $keywords = ['bán chạy', 'best seller', 'hot', 'phổ biến', 'nổi tiếng', 'được yêu thích'];
        foreach ($keywords as $keyword) {
            if (strpos($question, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Kiểm tra có phải câu hỏi về ngân sách không
     */
    private function isBudgetQuestion(string $question): bool
    {
        $keywords = ['dưới', 'khoảng', 'tầm', 'dưới', 'trên', 'triệu', 'nghìn', 'k'];
        $hasNumber = preg_match('/\d+/', $question);
        foreach ($keywords as $keyword) {
            if (strpos($question, $keyword) !== false && $hasNumber) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Xử lý câu hỏi về giá
     */
    private function handlePriceQuestion(string $question): string
    {
        // Tìm số giá trong câu hỏi - nhiều pattern khác nhau
        $targetPrice = null;
        
        // Pattern 1: Số có dấu chấm/phẩy (100.000, 1.000.000)
        if (preg_match('/(\d{1,3}(?:[.,]\d{3})+)/', $question, $matches)) {
            $targetPrice = (int)str_replace(['.', ','], '', $matches[1]);
        }
        // Pattern 2: Số thuần (100000, 50000) - ít nhất 4 chữ số
        elseif (preg_match('/(\d{4,})/', $question, $matches)) {
            $targetPrice = (int)$matches[1];
        }
        // Pattern 3: Số + đơn vị (100 nghìn, 1 triệu, 100k)
        elseif (preg_match('/(\d+)\s*(nghìn|k|triệu|đ)/i', $question, $matches)) {
            $price = (int)$matches[1];
            $unit = mb_strtolower(trim($matches[2]), 'UTF-8');
            if (strpos($unit, 'triệu') !== false) {
                $targetPrice = $price * 1000000;
            } elseif (strpos($unit, 'nghìn') !== false || $unit === 'k') {
                $targetPrice = $price * 1000;
            } else {
                $targetPrice = $price;
            }
        }
        // Pattern 4: Số nhỏ hơn 1000 (có thể là nghìn)
        elseif (preg_match('/(\d{1,3})(?:\s|$)/', $question, $matches)) {
            $price = (int)$matches[1];
            // Nếu có từ "nghìn" hoặc "k" gần đó, nhân 1000
            if (preg_match('/\d+\s*(nghìn|k)/i', $question)) {
                $targetPrice = $price * 1000;
            }
        }
        
        // Nếu có giá cụ thể, tìm sản phẩm gần giá đó
        if ($targetPrice !== null) {
            // Tìm sản phẩm có giá bằng hoặc gần giá mục tiêu (sai số ±10%)
            $tolerance = $targetPrice * 0.1;
            $products = DB::table('products')
                ->select('id', 'name', 'price', 'brand')
                ->whereBetween('price', [$targetPrice - $tolerance, $targetPrice + $tolerance])
                ->orderByRaw('ABS(price - ?)', [$targetPrice])
                ->limit(5)
                ->get();
            
            if ($products->isNotEmpty()) {
                $productList = [];
                foreach ($products as $product) {
                    $productList[] = "**{$product->name}** ({$product->brand}) - " . number_format($product->price) . "đ. /products/{$product->id}";
                }
                return "Các sản phẩm **khoảng " . number_format($targetPrice) . "đ**:\n" . implode("\n", $productList);
            } else {
                // Nếu không tìm thấy, tìm sản phẩm gần nhất
                $closestProduct = DB::table('products')
                    ->select('id', 'name', 'price', 'brand')
                    ->orderByRaw('ABS(price - ?)', [$targetPrice])
                    ->first();
                
                if ($closestProduct) {
                    return "Không tìm thấy sản phẩm giá " . number_format($targetPrice) . "đ. Sản phẩm gần nhất: **{$closestProduct->name}** ({$closestProduct->brand}) - " . number_format($closestProduct->price) . "đ. /products/{$closestProduct->id}";
                }
            }
        }
        
        // Tìm tên sản phẩm trong câu hỏi (nếu có)
        $products = DB::table('products')
            ->select('id', 'name', 'price', 'brand')
            ->limit(20)
            ->get();
        
        foreach ($products as $product) {
            $productName = mb_strtolower($product->name, 'UTF-8');
            $brandName = mb_strtolower($product->brand, 'UTF-8');
            if (strpos($question, $productName) !== false || strpos($question, $brandName) !== false) {
                return "Sản phẩm **{$product->name}** ({$product->brand}) có giá: " . number_format($product->price) . "đ. /products/{$product->id}";
            }
        }
        
        // Nếu không tìm thấy, trả về khoảng giá và gợi ý
        $minPrice = DB::table('products')->min('price');
        $maxPrice = DB::table('products')->max('price');
        $avgPrice = DB::table('products')->avg('price');
        
        return "Shop 46 Perfume có các sản phẩm với giá từ " . number_format($minPrice) . "đ đến " . number_format($maxPrice) . "đ (giá trung bình: " . number_format((int)$avgPrice) . "đ).\n\nBạn muốn tìm sản phẩm giá bao nhiêu? Ví dụ: 'sản phẩm giá 100000' hoặc 'dưới 1 triệu'";
    }
    
    /**
     * Xử lý câu hỏi về mùi hương
     */
    private function handleScentQuestion(string $question): string
    {
        $scents = DB::table('variants_scents')
            ->join('product_variants', 'variants_scents.id', '=', 'product_variants.scent_id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->select('variants_scents.scent_name', 'products.id', 'products.name', 'products.brand')
            ->distinct()
            ->limit(10)
            ->get();
        
        $scentList = [];
        foreach ($scents as $scent) {
            $scentName = mb_strtolower($scent->scent_name, 'UTF-8');
            if (strpos($question, $scentName) !== false) {
                $scentList[] = "**{$scent->name}** ({$scent->brand}) - Mùi {$scent->scent_name}. /products/{$scent->id}";
            }
        }
        
        if (!empty($scentList)) {
            return "Các sản phẩm có mùi hương bạn tìm:\n" . implode("\n", array_slice($scentList, 0, 5));
        }
        
        return "Shop có nhiều loại mùi hương đa dạng như: hoa cỏ, gỗ, hương trái cây, hương biển... Bạn thích mùi hương nào? Tôi có thể gợi ý sản phẩm phù hợp!";
    }
    
    /**
     * Xử lý câu hỏi về thương hiệu
     */
    private function handleBrandQuestion(string $question): string
    {
        $brands = DB::table('products')
            ->select('brand')
            ->distinct()
            ->limit(10)
            ->get()
            ->pluck('brand')
            ->toArray();
        
        foreach ($brands as $brand) {
            $brandLower = mb_strtolower($brand, 'UTF-8');
            if (strpos($question, $brandLower) !== false) {
                $products = DB::table('products')
                    ->where('brand', $brand)
                    ->select('id', 'name', 'price')
                    ->limit(5)
                    ->get();
                
                $productList = [];
                foreach ($products as $product) {
                    $productList[] = "**{$product->name}** - " . number_format($product->price) . "đ. /products/{$product->id}";
                }
                
                return "Shop có các sản phẩm của thương hiệu **{$brand}**:\n" . implode("\n", $productList);
            }
        }
        
        return "Shop có nhiều thương hiệu nước hoa nổi tiếng. Bạn quan tâm đến thương hiệu nào? Tôi có thể giới thiệu các sản phẩm phù hợp!";
    }
    
    /**
     * Xử lý câu hỏi về giới tính
     */
    private function handleGenderQuestion(string $question): string
    {
        $isMale = strpos($question, 'nam') !== false || strpos($question, 'male') !== false || strpos($question, 'đàn ông') !== false;
        $isFemale = strpos($question, 'nữ') !== false || strpos($question, 'female') !== false || strpos($question, 'phụ nữ') !== false;
        
        if ($isMale) {
            $products = DB::table('products')
                ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
                ->where('product_variants.gender', 'Nam')
                ->select('products.id', 'products.name', 'products.brand', 'products.price')
                ->distinct()
                ->limit(5)
                ->get();
        } elseif ($isFemale) {
            $products = DB::table('products')
                ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
                ->where('product_variants.gender', 'Nữ')
                ->select('products.id', 'products.name', 'products.brand', 'products.price')
                ->distinct()
                ->limit(5)
                ->get();
        } else {
            return "Bạn đang tìm nước hoa cho nam hay nữ? Tôi có thể gợi ý sản phẩm phù hợp!";
        }
        
        if ($products->isEmpty()) {
            return "Hiện tại shop chưa có sản phẩm phù hợp. Vui lòng thử lại sau!";
        }
        
        $gender = $isMale ? 'Nam' : 'Nữ';
        $productList = [];
        foreach ($products as $product) {
            $productList[] = "**{$product->name}** ({$product->brand}) - " . number_format($product->price) . "đ. /products/{$product->id}";
        }
        
        return "Các sản phẩm nước hoa dành cho **{$gender}**:\n" . implode("\n", $productList);
    }
    
    /**
     * Xử lý câu hỏi về sản phẩm bán chạy
     */
    private function handleBestSellerQuestion(): string
    {
        // Lấy sản phẩm có nhiều đơn hàng nhất hoặc có review tốt
        $products = DB::table('products')
            ->leftJoin('order_details', 'products.id', '=', 'order_details.product_id')
            ->select('products.id', 'products.name', 'products.brand', 'products.price', DB::raw('COUNT(order_details.id) as order_count'))
            ->groupBy('products.id', 'products.name', 'products.brand', 'products.price')
            ->orderBy('order_count', 'desc')
            ->limit(5)
            ->get();
        
        if ($products->isEmpty()) {
            $products = DB::table('products')
                ->select('id', 'name', 'brand', 'price')
                ->limit(5)
                ->get();
        }
        
        $productList = [];
        foreach ($products as $product) {
            $productList[] = "**{$product->name}** ({$product->brand}) - " . number_format($product->price) . "đ. /products/{$product->id}";
        }
        
        return "Các sản phẩm **bán chạy** tại shop:\n" . implode("\n", $productList);
    }
    
    /**
     * Xử lý câu hỏi về ngân sách
     */
    private function handleBudgetQuestion(string $question): string
    {
        // Tìm số trong câu hỏi
        preg_match('/(\d+)\s*(triệu|nghìn|k|đ)/i', $question, $matches);
        
        if (empty($matches)) {
            preg_match('/(\d+)/', $question, $matches);
        }
        
        if (!empty($matches[1])) {
            $budget = (int)$matches[1];
            
            // Chuyển đổi sang VNĐ
            if (isset($matches[2])) {
                $unit = mb_strtolower($matches[2], 'UTF-8');
                if (strpos($unit, 'triệu') !== false) {
                    $budget *= 1000000;
                } elseif (strpos($unit, 'nghìn') !== false || $unit === 'k') {
                    $budget *= 1000;
                }
            }
            
            $products = DB::table('products')
                ->select('id', 'name', 'brand', 'price')
                ->where('price', '<=', $budget)
                ->orderBy('price', 'desc')
                ->limit(5)
                ->get();
            
            if ($products->isEmpty()) {
                return "Không tìm thấy sản phẩm nào trong khoảng giá " . number_format($budget) . "đ. Bạn có muốn xem sản phẩm ở khoảng giá khác không?";
            }
            
            $productList = [];
            foreach ($products as $product) {
                $productList[] = "**{$product->name}** ({$product->brand}) - " . number_format($product->price) . "đ. /products/{$product->id}";
            }
            
            return "Các sản phẩm **dưới " . number_format($budget) . "đ**:\n" . implode("\n", $productList);
        }
        
        return "Bạn muốn tìm sản phẩm ở khoảng giá nào? Ví dụ: 'dưới 1 triệu', 'khoảng 500k'... Tôi sẽ gợi ý sản phẩm phù hợp!";
    }
    
    /**
     * Tìm kiếm sản phẩm theo từ khóa
     */
    private function searchProducts(string $question): string
    {
        // Tách từ khóa
        $keywords = explode(' ', $question);
        $keywords = array_filter($keywords, function($word) {
            return mb_strlen($word, 'UTF-8') > 2; // Bỏ các từ ngắn
        });
        
        if (empty($keywords)) {
            return '';
        }
        
        $products = DB::table('products')
            ->select('id', 'name', 'brand', 'price')
            ->where(function($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhere('name', 'LIKE', "%{$keyword}%")
                          ->orWhere('brand', 'LIKE', "%{$keyword}%");
                }
            })
            ->limit(5)
            ->get();
        
        if ($products->isEmpty()) {
            return '';
        }
        
        $productList = [];
        foreach ($products as $product) {
            $productList[] = "**{$product->name}** ({$product->brand}) - " . number_format($product->price) . "đ. /products/{$product->id}";
        }
        
        return "Tìm thấy các sản phẩm phù hợp:\n" . implode("\n", $productList);
    }
    
    /**
     * Câu trả lời mặc định
     */
    private function getDefaultResponse(): string
    {
        $responses = [
            "Tôi có thể giúp bạn tìm hiểu về các sản phẩm nước hoa, giá cả, mùi hương. Bạn muốn biết gì?",
            "Bạn có thể hỏi tôi về: giá sản phẩm, mùi hương, thương hiệu, hoặc sản phẩm phù hợp với giới tính/ngân sách của bạn.",
            "Shop 46 Perfume có nhiều sản phẩm nước hoa đa dạng. Bạn muốn tư vấn về sản phẩm nào?",
        ];
        
        return $responses[array_rand($responses)];
    }
}

