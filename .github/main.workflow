workflow "Add label to PR" {
  on = "pull_request"
  resolves = "PR Labeler"
}

workflow "Push" {
  on = "push"
  resolves = ["Draft Release"]
}


action "PR opened filter" {
  uses = "actions/bin/filter@master"
  args = "action opened"
}

action "PR Labeler" {
  needs = "PR opened filter"
  uses = "TimonVS/pr-labeler@master"
  secrets = ["GITHUB_TOKEN"]
}

action "Draft Release" {
  uses = "toolmantim/release-drafter@v5.1.1"
  secrets = ["GITHUB_TOKEN"]
}
