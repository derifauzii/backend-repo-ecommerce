<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function Home() {
        $allsubcategories = Subcategory::latest()->get();
        return view('admin.allsubcategory',compact('allsubcategories'));
    }
    public function AddSubCategory(){
        $categories = Category::latest()->get();
        return view('admin.addsubcategory', compact('categories'));
    }

    public function StoreSubCategory( Request $request) {
        $request->validate([
            'subcategory_name' => 'required|unique:subcategories',
            'category_id' => 'required',
        ]);

        $category_id = $request->category_id;
        $category_name = Category::where('id',$category_id)->value('category_name');

        SubCategory::insert([
            'subcategory_name' => $request->subcategory_name,
            'slug' => strtolower(str_replace(' ','-',$request->subcategory_name)),
            'category_id' => $category_id,
            'category_name' => $category_name,
        ]);

        Category::where('id',$category_id)->increment('subcategory_count',1);

        return redirect()->route('allsubcategory')->with('message','Sub Category Added Successfully');
    }

    public function EditSubCategory($id) {
        $subcategory_info = Subcategory::findOrFail($id);

        return view('admin.editsubcategory', compact('subcategory_info'));
    }

    public function UpdateSubCategory(Request $request) {
        $request->validate([
            'subcategory_name' => 'required|unique:subcategories',
        ]);

        $subcategory_id = $request->subcategoryid;

        Subcategory::findOrFail($subcategory_id)->update([
            'subcategory_name' => $request->subcategory_name,
            'slug' => strtolower(str_replace(' ','-',$request->subcategory_name)),
        ]);

        return redirect()->route('allsubcategory')->with('message','Sub Category Updated Successfully');
    }

    public function DeleteSubCategory($id) {
        $cat_id = Subcategory::where('id',$id)->value('category_id');
        SubCategory::findOrFail($id)->delete();

        Category::where('id',$cat_id)->decrement('subcategory_count',1);

        return redirect()->route('allsubcategory')->with('message','Sub Category Deleted Successfully');
    }


}
