<?php

use Alfred\Workflow;

class PHPHubWorkFlow
{
    private $query = null;
    private $workflow = null;

    public function __construct($query)
    {
        $this->query = $query;
        $this->workflow = new Workflow('me.naux.phphub');
    }

    public function run()
    {
        list($key, $word) = explode('\ ', $this->query, 2);

        if (in_array($key, ['n', 'new', ''])) {
            $this->recentTopics();
        } elseif (in_array($key, ['u', 'user'])) {
            $this->userTopics($word);
        } elseif (in_array($key, ['s', 'search'])) {
            $this->search($word);
        }

        echo $this->workflow->toXML();
    }

    public function recentTopics()
    {
        $recent = file_get_contents('http://phphub.dev/topics/recent.json');

        foreach (json_decode($recent, true)as $value) {
            $this->workflow->result($value);
        }
    }

    public function userTopics($username)
    {
        $recent = file_get_contents("http://phphub.dev/users/{$username}/topics.json");

        $topics = json_decode($recent, true);

        $this->workflow->result([
            'arg'          => 'https://www.google.com/search?q=site:phphub.org%20'.$word,
            'title'        => "访问 {$username} 的个人主页",
        ]);

        if ($topics) {
            foreach (json_decode($recent, true)as $value) {
                $this->workflow->result($value);
            }
        }
    }

    public function search($word)
    {
        $this->workflow->result(array(
            'arg'          => 'https://www.google.com/search?q=site:phphub.org%20'.$word,
            'title'        => "Search for '{$word}'",
        ));
    }
}
