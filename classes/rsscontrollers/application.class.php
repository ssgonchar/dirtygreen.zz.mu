<?

class ApplicationRssController extends RssController
{
	function ApplicationRssController()
	{
		RssController::RssController();
	}

	function _authorize($page_access_role)
	{
		return true;
	}
}

