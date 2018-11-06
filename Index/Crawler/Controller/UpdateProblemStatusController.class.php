<?php
/**
 *
 * Created by Dream.
 * User: Boxjan
 * Datetime: 11/3/18 1:53 PM
 */

namespace Crawler\Controller;


use Basic\Log;
use Crawler\Common\Person;
use Crawler\Model\ProblemModel;
use Crawler\Model\StudentAccountModel;
use Crawler\Fetcher\POJFetcher;
use Crawler\Fetcher\HDOJFetcher;
use Crawler\Fetcher\BestCodeOJFetcher;
use Crawler\Fetcher\CodeForceOJFetcher;
use Crawler\Fetcher\SDIBTOJFetcher;
use Crawler\Fetcher\SDUTOJFetcher;


class UpdateProblemStatusController extends BaseController
{
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $ojList = C('SUPPORT_GET_AC_INFO_OJ');
        foreach ($ojList as $item) {
            $this->$item();
        }
    }

    public function __call($name, $arguments) {
        $ojList = C('SUPPORT_GET_AC_INFO_OJ');
        $ojCrawlerName = C('OJ_TO_CRAWLER_NAME');

        if (!in_array($name, $ojList)) Log::warn("require: ask to crawler oj: {}, but not support", $name);

        $problemList = ProblemModel::instance()->getProblemByOJName($name);

        $stuAccountList = StudentAccountModel::instance()->getAccountByOJName($name);

        $fetcherName = 'Crawler\\Fetcher\\'. $ojCrawlerName[$name] . 'Fetcher';

        $handle = new $fetcherName();

        foreach ($problemList as $problem) {
            foreach ($stuAccountList as $stu) {
                $res = $handle->getProblemStatus(new Person($stu['user_id'], $stu['origin_id']), $problem['origin_id']);
                Log::debug("" ,$name, $problem['origin_id'] , $res);
            }
        }
    }

}