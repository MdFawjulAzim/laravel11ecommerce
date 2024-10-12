<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
Use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class AdminController extends Controller
{
    public function index(){
        return view('admin.index');
    }
    public function brands(){
        $brands = Brand::orderBy('id','DESC')->paginate(10);
        return view('admin.brands',compact('brands'));
    }
    public function add_brand(){
        return view('admin.brand-add');
    }
    public function brand_store(Request $request) {
        // Validate the input including making image required
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048'
        ], [
            // Custom error message for image
            'image.required' => 'Please upload an image for the brand.',
            'image.mimes' => 'The image must be a file of type: png, jpg, jpeg.',
            'image.max' => 'The image size must not exceed 2MB.'
        ]);
    
        $brand = new Brand();
        $brand->name = $request->name;
    
        // Generate the slug from $request->name
        $brand->slug = Str::slug($request->name);
    
        // Image Upload
        $image = $request->file('image');
        $file_extension= $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp.'.'.$file_extension;
        $this->GenerateBrandThumbnailsImage($image,$file_name);
        $brand->image =$file_name;
        $brand->save();
    
        // Redirect and display success message
        return redirect()->route('admin.brands')->with('status', 'Brand Has Been Added Successfully!');
    }
    
    public function brand_edit($id){
        $brand = Brand::find($id);
        return view('admin.brand-edit',compact('brand'));
    }
    public function brand_update(Request $request) {
        // Validate input and set conditions for the image
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,' . $request->id,
            'image' => 'nullable|mimes:png,jpg,jpeg|max:2048'
        ], [
            'image.mimes' => 'Image must be a file of type: png, jpg, jpeg.',
            'image.max' => 'Image size must not exceed 2MB.',
        ]);
    
        $brand = Brand::find($request->id);
        
        // Update the name and slug
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name); // Use $request->name to update the slug
    
        // Check if an image file is uploaded
        if ($request->hasFile('image')) {
            // Delete the old image
            if (File::exists(public_path('uploads/brands/' . $brand->image))) {
                File::delete(public_path('uploads/brands/' . $brand->image));
            }
        }
        // Image Upload
        $image = $request->file('image');
        $file_extension= $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp.'.'.$file_extension;
        $this->GenerateBrandThumbnailsImage($image,$file_name);
        $brand->image =$file_name;
        $brand->save();
        // Redirect and display success message
        return redirect()->route('admin.brands')->with('status', 'Brand Has Been Updated Successfully!');

        }
    
    public function GenerateBrandThumbnailsImage($image, $imageName) {
        $destinationPath = public_path('uploads/brands');
        $img = Image::read($image->path());
        $img->cover(124,124,"top");
        $img->resize(124,124,function($constraint){
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);
    }

    public function brand_delete($id) {
        $brand = Brand::find($id);
        
        // Delete the old image
        if (File::exists(public_path('uploads/brands/'. $brand->image))) {
            File::delete(public_path('uploads/brands/'. $brand->image));
        }
        
        // Delete the brand
        $brand->delete();

        // Redirect and display success message
        return redirect()->route('admin.brands')->with('status', 'Brand Has Been Deleted Successfully!');
    }
    // ----------------------------------------------------------------

    //Category 
    public function categories(){
        $categories = Category::orderBy('id','DESC')->paginate(10);
        return view('admin.categories',compact('categories'));

    }
    public function add_category(){
        return view('admin.category-add');
    }
    public function category_store(Request $request) {
       // Validate the input including making image required
       $request->validate([
        'name' => 'required',
        'slug' => 'required|unique:categories,slug',
        'image' => 'required|mimes:png,jpg,jpeg|max:2048'
    ], [
        // Custom error message for image
        'image.required' => 'Please upload an image for the brand.',
        'image.mimes' => 'The image must be a file of type: png, jpg, jpeg.',
        'image.max' => 'The image size must not exceed 2MB.'
    ]);

    $category = new Category();
    $category->name = $request->name;

    // Generate the slug from $request->name
    $category->slug = Str::slug($request->name);

    // Image Upload
    $image = $request->file('image');
    $file_extension= $request->file('image')->extension();
    $file_name = Carbon::now()->timestamp.'.'.$file_extension;
    $this->GenerateCategoryThumbnailsImage($image,$file_name);
    $category->image =$file_name;
    $category->save();

    // Redirect and display success message
    return redirect()->route('admin.categories')->with('status', 'category Has Been Added Successfully!');
        
    }

    public function GenerateCategoryThumbnailsImage($image, $imageName) {
        $destinationPath = public_path('uploads/categories');
        $img = Image::read($image->path());
        $img->cover(124,124,"top");
        $img->resize(124,124,function($constraint){
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);
    }
    public function category_edit($id){
        $category = Category::find($id);
        return view('admin.category-edit',compact('category'));
    }

    public function category_update(Request $request){
         // Validate input and set conditions for the image
         $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,' . $request->id,
            'image' => 'nullable|mimes:png,jpg,jpeg|max:2048'
        ], [
            'image.mimes' => 'Image must be a file of type: png, jpg, jpeg.',
            'image.max' => 'Image size must not exceed 2MB.',
        ]);
    
        $category = Category::find($request->id);
        
        // Update the name and slug
        $category->name = $request->name;
        $category->slug = Str::slug($request->name); // Use $request->name to update the slug
    
        // Check if an image file is uploaded
        if ($request->hasFile('image')) {
            // Delete the old image
            if (File::exists(public_path('uploads/categories/' . $category->image))) {
                File::delete(public_path('uploads/categories/' . $category->image));
            }
        }
        // Image Upload
        $image = $request->file('image');
        $file_extension= $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp.'.'.$file_extension;
        $this->GenerateCategoryThumbnailsImage($image,$file_name);
        $category->image =$file_name;
        $category->save();
        // Redirect and display success message
        return redirect()->route('admin.categories')->with('status', 'category Has Been Updated Successfully!');
    }

    public function category_delete($id) {
        $category = Category::find($id);
        
        // Delete the old image
        if (File::exists(public_path('uploads/categories/'. $category->image))) {
            File::delete(public_path('uploads/categories/'. $category->image));
        }
        
        // Delete the brand
        $category->delete();

        // Redirect and display success message
        return redirect()->route('admin.categories')->with('status', 'category Has Been Deleted Successfully!');
    }

    // ----------------------------------------------------------------


    //Product
    public function products(){
        $products = Product::orderBy('created_at','DESC')->paginate(10);
        return view('admin.products',compact('products'));
    }
    
}
