
    public function reactivateAccount(Request $request)
    {
        if (!$this->isManagement()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $account = UserAccountNumbers::where('account_number', $request->account_number)
                                       ->where('comp_id', \Auth::user()->comp_id)
                                       ->first();

        if (!$account) {
            return response()->json(['success' => false, 'message' => 'Account not found'], 404);
        }

        $account->account_status = 'active';
        $account->save();

        return response()->json(['success' => true, 'message' => 'Account has been re-activated.']);
    }
