<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\ReviewService;
use Core\Controller;
use Core\Request;
use Core\Response;
use Core\Session;

class ReviewController extends Controller
{
	private ReviewService $reviews;

	public function __construct(Request $request, Response $response)
	{
		parent::__construct($request, $response);
		$this->reviews = new ReviewService();
	}

	public function create(): void
	{
		$actor     = $this->user();
		$bookingId = (int) $this->request->query('booking_id', 0);
		$reviewModel = new \App\Models\Review();

		$alreadyReviewed = is_array($actor) && $bookingId > 0
			? $reviewModel->existsForBookingAndReviewer($bookingId, (int) $actor['id'])
			: false;

		$this->view('reviews.create', [
			'title'           => 'Write a Review',
			'bookingId'       => $bookingId,
			'alreadyReviewed' => $alreadyReviewed,
		]);
	}

	public function store(): void
	{
		$actor = $this->user();
		if (!is_array($actor)) {
			$this->redirect('/login');
			return;
		}

		[$valid, $errors, $clean] = $this->validate($this->request->all(), [
			'booking_id' => 'required|integer|min:1',
			'rating' => 'required|integer|in:1,2,3,4,5',
			'comment' => 'nullable|string|max:1000',
		]);

		if (!$valid) {
			$this->redirect('/reviews/create?booking_id=' . (int) ($this->request->input('booking_id') ?? 0));
			return;
		}

		try {
			$this->reviews->createReview($actor, $clean);
			Session::flash('success', 'Review submitted successfully.');
			$this->redirect('/bookings/mine');
		} catch (\Throwable $exception) {
			Session::flash('errors', ['review' => [$exception->getMessage()]]);
			Session::flash('old', $clean);
			$this->redirect('/reviews/create?booking_id=' . (int) ($clean['booking_id'] ?? 0));
		}
	}
}
