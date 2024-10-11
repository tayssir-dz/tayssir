<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\Cards\GetCardsRequest;
use App\Models\Card;
use Illuminate\Http\Request;
use Validator;

class CardsController extends BaseController
{
    public function index(GetCardsRequest $request)
    {
        $page = $request->query('page') ?? 1;
        $cards = Card::paginate(5, ['*'], 'page', $page);
        return response()->json($cards);
    }

    public function cardById(Request $request)
    {
        $id = $request->id;
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $card = Card::find($id);
        if (!$card) {
            return $this->sendError('Card not found');
        }

        return $this->sendResponse(['card' => $card], 'Card retrieved successfully');
    }

    public function createCards(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'number_of_cards' => 'required|integer|min:1',
            'expires_at' => 'required|date_format:d-m-Y',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $cards = [];
        for ($i = 0; $i < $request->number_of_cards; $i++) {
            $code = rand(100000000000, 999999999999);
            $card = Card::create([
                'code' => $code,
                'expires_at' => $request->expires_at,
            ]);
            $cards[] = $card;
        }

        return $this->sendResponse(['cards' => $cards], 'Cards created successfully');
    }

    public function deleteCard(Request $request)
    {
        $id = $request->id;
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer',
        ]);


        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $card = Card::find($id);
        if (!$card) {
            return $this->sendError('Card not found');
        }
        $card->delete();
        return $this->sendResponse([], 'Card deleted successfully');
    }
}
