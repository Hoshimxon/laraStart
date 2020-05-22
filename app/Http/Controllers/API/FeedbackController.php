<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $feedback = Feedback::query()->paginate();
        return success_out($feedback, true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function create(Request $request)
    {
        $request->merge(['phone' => clearPhone($request->phone)]);

        $data = $request->validate([
            'company' => 'nullable|string|min:3|max:255',
            'name' => 'required|string|min:2|max:255',
            'phone' => 'required|int|digits_between:9,12',
            'email' => 'required|email|max:255'
        ]);

        $data['ip'] = $request->ip();
        $item = new Feedback();
        $item->fill($data);

        if ($item->save())
            return success_out($item);

        return error_out([], 422, 'Error saving model!');
    }

    /**
     * Display the specified resource.
     *
     * @param Feedback $feedback
     * @return Response
     */
    public function show(Feedback $feedback)
    {
        return success_out($feedback);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Feedback $feedback
     * @return Response
     * @throws \Exception
     */
    public function destroy(Feedback $feedback)
    {
        $feedback->delete();
        return success_out($feedback);
    }
}
