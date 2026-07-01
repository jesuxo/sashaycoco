<?php

namespace App\Http\Controllers;

use App\Models\Ia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IAController extends Controller
{
    public function index()
    {
        $knowledgeItems = Ia::ordered()->paginate(10);
        return view('ia', compact('knowledgeItems'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'    => 'required|string|max:255',
            'text'     => 'required|string',
            'category' => 'nullable|string|max:100',
            'tags'     => 'nullable'
        ]);

        if ($validator->fails()) {
            dd($validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $knowledge = Ia::create([
            'title'    => $request->title,
            'text'     => (isset($request->text))?$request->text: '',
            'category' => $request->category,
            'tags'     => $request->tags,
            'active'   => true
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Información agregada correctamente',
                'data'    => $knowledge
            ]);
        }

        return redirect()->route('iaknowledge.index')
            ->with('success', 'Información agregada correctamente');
    }

    public function update(Request $request, $id)
    {
        $knowledge = Ia::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title'    => 'required|string|max:255',
            'text'     => 'required|string',
            'category' => 'nullable|string|max:100',
            'tags'     => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $knowledge->update([
            'title'    => $request->title,
            'text'     => $request->text,
            'category' => $request->category,
            'tags'     => $request->tags,
            'active'   => $request->has('active') ? true : false
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Información actualizada correctamente',
                'data'    => $knowledge
            ]);
        }

        return redirect()->route('iaknowledge.index')
            ->with('success', 'Información actualizada correctamente');
    }

    public function destroy($id)
    {
        $knowledge = Ia::findOrFail($id);
        $knowledge->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Información eliminada correctamente'
            ]);
        }

        return redirect()->route('iaknowledge.index')
            ->with('success', 'Información eliminada correctamente');
    }

    public function search(Request $request)
    {
        $query = $request->get('query');

        $results = Ia::active()
            ->where(function($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                    ->orWhere('text', 'LIKE', "%{$query}%")
                    ->orWhere('category', 'LIKE', "%{$query}%");
            })
            ->ordered()
            ->get();

        return response()->json($results);
    }
}
