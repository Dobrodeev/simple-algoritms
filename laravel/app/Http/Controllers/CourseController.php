<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Services\CourseJsonStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CourseController extends Controller
{
    /** Table with courses from the database. */
    public function index(): View
    {
        return view('courses.index', [
            'courses' => Course::orderBy('symbol')->get(['symbol', 'price']),
        ]);
    }

    /** Add a course to the database. */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, Rule::unique('courses', 'symbol'));

        Course::create($data);

        return redirect()->route('courses.index')->with('status', "Пара {$data['symbol']} добавлена в базу данных.");
    }

    /** Table with courses from the JSON file. */
    public function json(CourseJsonStore $store): View
    {
        return view('courses.json', ['courses' => $store->all()]);
    }

    /** Add (or update) a course in the JSON file. */
    public function storeJson(Request $request, CourseJsonStore $store): RedirectResponse
    {
        $data = $this->validated($request);

        $store->put($data['symbol'], $data['price']);

        return redirect()->route('courses.json')->with('status', "Пара {$data['symbol']} записана в JSON-файл.");
    }

    /** @return array{symbol: string, price: string} */
    private function validated(Request $request, mixed ...$symbolRules): array
    {
        $request->merge(['symbol' => strtoupper(trim((string) $request->input('symbol')))]);

        return $request->validate([
            'symbol' => ['required', 'string', 'max:20', 'regex:/^[A-Z0-9]+$/', ...$symbolRules],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999999999999'],
        ], [], ['symbol' => 'валютная пара', 'price' => 'курс']);
    }
}
