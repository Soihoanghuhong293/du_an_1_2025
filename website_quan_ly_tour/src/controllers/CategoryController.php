<?php

class CategoryController
{
    public function index(): void
{
    // 1. Nạp model (giữ nguyên)
    require_once __DIR__ . '/../models/Category.php';

    $categories = Category::all();

   
    view('categories.index', [
        'categories' => $categories,
        'title'      => 'Quản lý Danh mục'
    ]);
}
    public function delete($id=null)
    {
        if($id){
            require_once __DIR__ .'/../models/Category.php';
            Category::deleteById($id);
        }
         header("Location: index.php?act=categories");
         exit;
    }
     public function add()
     {
        require_once __DIR__ .'/../models/Category.php';
        if($_SERVER['REQUEST_METHOD']==='POST'){

            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $status = $_POST['status'] ?? 1;


                Category:: create($name,$description,$status);
                 header("Location: index.php?act=categories");
                 exit;
          }
          $title ="them danh muc";
          ob_start();
    require __DIR__ . '/../../views/categories/form.php';
    $content = ob_get_clean();

    require __DIR__ . '/../../views/layouts/AdminLayout.php';

     }

     public function edit($id){
        require_once __DIR__ .'/../models/Category.php';
        $category = Category::find($id);

        if(!$category){
            echo" danh mục không tồn tại";
            exit;

        }
        if($_SERVER['REQUEST_METHOD']==='POST'){

            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';

            $status= $_POST['status'] ?? 1;

             Category::updateById($id, $name, $description, $status);
             header("Location: index.php?act=categories");
        exit;

        }
         $title = "Sửa danh mục";

    ob_start();
    require __DIR__ . '/../../views/categories/form.php';
    $content = ob_get_clean();

    require __DIR__ . '/../../views/layouts/AdminLayout.php';
     }

    // API trả về thông tin Tour (Dùng cho Ajax ở trang Create Booking)
    public function getTourInfo()
    {
        // Xóa bộ nhớ đệm output để đảm bảo JSON sạch, không bị lỗi cú pháp do khoảng trắng thừa
        if (ob_get_length()) ob_clean(); 
        
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tourId = $_POST['tour_id'] ?? null;

            if ($tourId) {
                // Gọi hàm mới vừa viết bên Model
                $tour = Booking::getTourById($tourId);

                if ($tour) {
                    echo json_encode(['status' => 'success', 'data' => $tour]);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy Tour']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Thiếu ID Tour']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
        }
        exit; 
    }
}
